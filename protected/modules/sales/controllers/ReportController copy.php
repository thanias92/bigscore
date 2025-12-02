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
        $filterModel = new ReportFilterForm();
        $searchModel = new DealStageSearch();

        // Muat data filter dari URL (jika ada)
        $filterModel->load(Yii::$app->request->get());

        // Ambil data yang sudah diproses oleh SearchModel
        $dataProvider = $searchModel->search($filterModel);

        // Kirim data ke view
        return $this->render('deals-by-stage', [
            'dataProvider' => $dataProvider,
            'filterModel' => $filterModel,
        ]);
    }

    public function actionDealWon()
    {
        $filterModel = new ReportFilterForm();
        $searchModel = new DealWonReportSearch();

        $filterModel->load(Yii::$app->request->get());

        $dataProvider = $searchModel->search($filterModel);

        return $this->render('deal-won', [
            'dataProvider' => $dataProvider,
            'filterModel' => $filterModel,
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
