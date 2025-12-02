<?php

namespace app\modules\sales;

use app\models\Deals; // Pastikan namespace model Deals Anda benar
use yii\data\ArrayDataProvider;
use yii\db\Query;

class DealStageSearch
{
    /**
     * Fungsi utama untuk mencari dan mengagregasi data
     * @param \app\models\ReportFilterForm $filterModel
     * @return ArrayDataProvider
     */
    public function search($filterModel)
    {
        $query = new Query();
        $query->select([
            'label_deals',
            'COUNT(deals_id) as deal_count',
            // LAKUKAN TYPE CASTING DI SINI
            'SUM(CAST(total AS numeric)) as opportunity_total'
        ])
            ->from(Deals::tableName())
            ->groupBy('label_deals');

        if ($filterModel->validate()) {
            $query->where(['between', 'created_at', $filterModel->startDate . ' 00:00:00', $filterModel->endDate . ' 23:59:59']);
        }

        $allData = $query->all();

        $dataProvider = new ArrayDataProvider([
            'allModels' => $allData,
            'pagination' => false,
            'sort' => [
                'attributes' => ['label_deals', 'deal_count', 'opportunity_total'],
            ],
        ]);

        return $dataProvider;
    }
}
