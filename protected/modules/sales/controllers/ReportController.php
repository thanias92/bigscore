<?php

namespace app\modules\sales\controllers;

use app\models\ReportFilterForm;
use app\modules\sales\DealStageSearch;
use app\modules\sales\DealWonReportSearch;
use app\modules\sales\ProductSalesReportSearch;
use app\modules\sales\CustomerBySalesSearch;
use app\modules\sales\DealsByRepresentativeSearch;
use app\modules\sales\DealCustomerSearch;
use Yii;
use yii\web\Controller;

class ReportController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionDealsByStage()
    {
        $filterModel = new \app\models\ReportFilterForm();
        $searchModel = new DealStageSearch();

        $filterModel->load(Yii::$app->request->get());
        $dataProvider = $searchModel->search($filterModel);

        // --- TAMBAHAN: Siapkan warna untuk chart ---
        $pipelineColors = [
            'New' => 'rgba(9, 148, 153, 0.3)',
            'Proposal Sent' => 'rgba(9, 148, 153, 0.5)',
            'Negotiation' => 'rgba(9, 148, 153, 0.8)',
            'Deal Won' => 'rgba(26, 161, 25, 1)',
            'Deal Lost' => 'rgba(201, 40, 30, 0.8)',
        ];

        $chartColors = [];
        foreach ($dataProvider->getModels() as $model) {
            $chartColors[] = $pipelineColors[$model['label_deals']] ?? '#ccc';
        }

        return $this->render('deals-by-stage', [
            'dataProvider' => $dataProvider,
            'filterModel' => $filterModel,
            'chartColors' => $chartColors, // Kirim data warna ke view
        ]);
    }

    public function actionDealWon()
    {
        $filterModel = new \app\models\ReportFilterForm();
        $searchModel = new DealWonReportSearch();

        $filterModel->load(Yii::$app->request->get());

        // 1. Dapatkan data untuk tabel (GridView) seperti biasa
        $dataProvider = $searchModel->search($filterModel);

        // --- PERBAIKAN: Buat query baru yang bersih untuk Chart ---

        // 2. Buat query baru dari awal, mulai dari model Deals
        $chartQuery = \app\models\Deals::find()
            ->joinWith('product') // Gunakan relasi untuk join ke tabel product
            ->where(['deals.label_deals' => 'Deal Won']);

        // 3. Terapkan filter tanggal yang sama ke query chart
        if ($filterModel->validate()) {
            $chartQuery->andWhere(['between', 'deals.purchase_date', $filterModel->startDate, $filterModel->endDate]);
        }

        // 4. Ambil data agregat untuk chart dari query yang baru
        $chartDataRaw = $chartQuery
            ->select(['{{product}}.product_name', 'SUM(CAST({{deals}}.total AS numeric)) as total_value'])
            ->groupBy(['{{product}}.product_name'])
            ->orderBy(['total_value' => SORT_DESC])
            ->limit(5)
            ->asArray()
            ->all();

        // 5. Olah data menjadi format yang siap digunakan Chart.js
        $chartLabels = \yii\helpers\ArrayHelper::getColumn($chartDataRaw, 'product_name');
        $chartValues = \yii\helpers\ArrayHelper::getColumn($chartDataRaw, 'total_value');

        // 6. Render ke file view dengan semua data yang diperlukan
        return $this->render('deal-won', [
            'dataProvider' => $dataProvider,
            'filterModel' => $filterModel,
            'chartLabels' => $chartLabels,
            'chartValues' => $chartValues,
        ]);
    }

    public function actionProductSales()
    {
        $filterModel = new ReportFilterForm();
        $searchModel = new ProductSalesReportSearch();

        $filterModel->load(Yii::$app->request->get());

        $dataProvider = $searchModel->search($filterModel);

        return $this->render('product-sales', [
            'dataProvider' => $dataProvider,
            'filterModel' => $filterModel,
        ]);
    }

    public function actionCustomerBySales()
    {
        $filterModel = new ReportFilterForm();
        $searchModel = new CustomerBySalesSearch();

        $filterModel->load(Yii::$app->request->get());

        $dataProvider = $searchModel->search($filterModel);

        return $this->render('customer-by-sales', [
            'dataProvider' => $dataProvider,
            'filterModel'  => $filterModel,
        ]);
    }

    public function actionDealsByCustomer()
    {
        $filterModel = new ReportFilterForm();
        $searchModel = new DealCustomerSearch();

        $filterModel->load(Yii::$app->request->get());

        $dataProvider = $searchModel->search($filterModel);

        return $this->render('deals-by-customer', [
            'dataProvider' => $dataProvider,
            'filterModel'  => $filterModel,
        ]);
    }

    public function actionDealsBySalesRepresentative()
    {
        $filterModel = new \app\models\ReportFilterForm();
        $searchModel = new \app\modules\sales\DealsByRepresentativeSearch();

        $filterModel->load(\Yii::$app->request->get());

        // 1. Dapatkan data untuk tabel (GridView) seperti biasa
        $dataProvider = $searchModel->search($filterModel);

        // --- PERBAIKAN: Buat query baru yang bersih untuk Chart ---

        // Dapatkan daftar ID salesman (logika yang sama seperti di SearchModel)
        $auth = \Yii::$app->authManager;
        $representativeIDs = $auth->getUserIdsByRole('Sales Representative');
        if (empty($representativeIDs)) {
            $representativeIDs = [0];
        }

        // Buat query baru dari awal
        $chartQuery = \app\models\Deals::find()
            ->joinWith('createdBy user') // Join dengan tabel user dan beri alias
            ->where(['in', '{{deals}}.created_by', $representativeIDs]);

        // Terapkan filter tanggal yang sama ke query chart
        if ($filterModel->validate()) {
            $chartQuery->andWhere(['between', '{{deals}}.created_at', $filterModel->startDate, $filterModel->endDate]);
        }

        // Ambil data agregat untuk chart
        $chartDataRaw = $chartQuery
            ->select(['user.username', 'COUNT({{deals}}.deals_id) as deal_count'])
            ->groupBy(['user.username'])
            ->orderBy(['deal_count' => SORT_DESC])
            ->asArray()
            ->all();

        // Olah data menjadi format yang siap digunakan Chart.js
        $chartLabels = \yii\helpers\ArrayHelper::getColumn($chartDataRaw, 'username');
        $chartValues = \yii\helpers\ArrayHelper::getColumn($chartDataRaw, 'deal_count');

        // Render ke file view
        return $this->render('deals-by-sales-representative', [
            'dataProvider' => $dataProvider,
            'filterModel'  => $filterModel,
            'chartLabels'  => $chartLabels,
            'chartValues'  => $chartValues,
        ]);
    }
}
