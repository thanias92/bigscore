<?php

namespace app\modules\sales;

use app\models\Customer;
use app\models\Deals;
use app\models\Product;
use app\models\User;
use app\models\CustomerVisit;
use Yii;
use yii\helpers\ArrayHelper;

class DashboardService
{
    /**
     * Helper function untuk memastikan semua tahapan pipeline ada dan berurutan.
     */
    private function getOrderedPipelineData($sourceData)
    {
        $orderedLabels = [
            'New' => 0,
            'Proposal Sent' => 0,
            'Negotiation' => 0,
            'Deal Won' => 0,
            'Deal Lost' => 0,
        ];

        foreach ($sourceData as $data) {
            if (isset($orderedLabels[$data['label_deals']])) {
                $orderedLabels[$data['label_deals']] = (int)$data['deal_count'];
            }
        }

        $finalData = [];
        foreach ($orderedLabels as $label => $count) {
            $finalData[] = ['label_deals' => $label, 'deal_count' => $count];
        }
        return $finalData;
    }

    public function getSalesSummary()
    {
        $totalCustomer = Customer::find()->where(['deleted_at' => null])->count();
        $totalSales = Deals::find()
            ->where(['label_deals' => 'Deal Won', 'deleted_at' => null])
            ->sum('CAST(total AS numeric)');

        return [
            'totalCustomer' => $totalCustomer,
            'totalSales' => $totalSales ?? 0,
        ];
    }

    public function getSalesPipelineData()
    {
        $data = Deals::find()
            ->select(['label_deals', 'COUNT(*) as deal_count'])
            ->where(['deleted_at' => null])
            ->groupBy('label_deals')
            ->asArray()
            ->all();

        return $this->getOrderedPipelineData($data);
    }

    public function getProductSoldData()
    {
        $query = (new \yii\db\Query())
            ->select(['p.product_name', 'SUM(d.unit_product) as total_units_sold'])
            ->from(['d' => Deals::tableName()])
            ->innerJoin(['p' => Product::tableName()], 'd.product_id = p.id_produk')
            ->where(['d.label_deals' => 'Deal Won'])
            ->andWhere(['d.deleted_at' => null])
            ->groupBy('p.product_name')
            ->orderBy(['total_units_sold' => SORT_DESC])
            ->limit(5);

        return $query->all();
    }

    public function getKeyPerformanceData()
    {
        $auth = Yii::$app->authManager;
        $salesManagerIDs = $auth->getUserIdsByRole('Sales Manager');
        $salesRepIDs = $auth->getUserIdsByRole('Sales Representative');
        $salesmenIDs = array_merge($salesManagerIDs, $salesRepIDs);

        if (empty($salesmenIDs)) {
            return ['salesmenCount' => 0, 'salesmenVisits' => []];
        }
        $salesmen = User::find()->where(['id' => $salesmenIDs])->all();
        $visitsThisMonth = CustomerVisit::find()
            ->select(['created_by', 'COUNT(*) as visit_count'])
            ->where(['between', 'visit_date', date('Y-m-01'), date('Y-m-t')])
            ->andWhere(['created_by' => $salesmenIDs])
            ->groupBy('created_by')
            ->asArray()
            ->all();

        $salesmenVisits = [];
        foreach ($salesmen as $salesman) {
            $salesmenVisits[$salesman->id] = ['id' => $salesman->id, 'username' => $salesman->username, 'visit_count' => 0];
        }
        foreach ($visitsThisMonth as $visit) {
            if (isset($salesmenVisits[$visit['created_by']])) {
                $salesmenVisits[$visit['created_by']]['visit_count'] = (int)$visit['visit_count'];
            }
        }

        return ['salesmenCount' => count($salesmen), 'salesmenVisits' => array_values($salesmenVisits)];
    }

    public function getIndividualSalesmanPerformance()
    {
        $auth = Yii::$app->authManager;
        $salesManagerIDs = $auth->getUserIdsByRole('Sales Manager');
        $salesRepIDs = $auth->getUserIdsByRole('Sales Representative');
        $salesmenIDs = array_merge($salesManagerIDs, $salesRepIDs);

        if (empty($salesmenIDs)) {
            return [];
        }

        $salesmen = User::find()->innerJoinWith('salesmanProfile')->where(['user.id' => $salesmenIDs])->all();
        $performanceData = [];

        foreach ($salesmen as $salesman) {
            $actualVisit = CustomerVisit::find()
                ->where(['created_by' => $salesman->id])
                ->andWhere(['between', 'visit_date', date('Y-m-01'), date('Y-m-t')])
                ->count();

            $targetVisit = $salesman->salesmanProfile->visit_target ?? 20;

            $pipelineRaw = Deals::find()
                ->select(['label_deals', 'COUNT(*) as deal_count'])
                ->where(['created_by' => $salesman->id, 'deleted_at' => null])
                ->groupBy('label_deals')
                ->asArray()
                ->all();

            $pipeline = $this->getOrderedPipelineData($pipelineRaw);

            $performanceData[] = [
                'id' => $salesman->id,
                'username' => $salesman->username,
                'visitData' => ['actual' => (int)$actualVisit, 'target' => (int)$targetVisit],
                'pipelineData' => $pipeline,
            ];
        }
        return $performanceData;
    }
}
