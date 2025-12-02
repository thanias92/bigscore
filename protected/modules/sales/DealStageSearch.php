<?php

namespace app\modules\sales;

use app\models\Deals;
use yii\data\ArrayDataProvider;
use yii\db\Query;

class DealStageSearch
{
    public function search($filterModel)
    {
        $query = new Query();
        $query->select([
            'label_deals',
            'COUNT(deals_id) as deal_count',
            'SUM(CAST(total AS numeric)) as opportunity_total'
        ])
            ->from(Deals::tableName())
            ->groupBy('label_deals');

        if ($filterModel->validate()) {
            // Gunakan created_at untuk filter tanggal yang lebih akurat
            $query->andWhere(['between', 'created_at', $filterModel->startDate . ' 00:00:00', $filterModel->endDate . ' 23:59:59']);
        }

        $allData = $query->all();

        // --- PERUBAHAN UTAMA: Pastikan semua stage ada dan berurutan ---
        $orderedData = [];
        $dealLabels = Deals::getDealsLabelList(); // Ambil urutan dari model

        // Buat struktur data default
        foreach ($dealLabels as $labelKey => $labelName) {
            $orderedData[$labelKey] = [
                'label_deals' => $labelName,
                'deal_count' => 0,
                'opportunity_total' => 0,
            ];
        }

        // Isi dengan data dari query
        foreach ($allData as $data) {
            if (isset($orderedData[$data['label_deals']])) {
                $orderedData[$data['label_deals']] = $data;
            }
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => array_values($orderedData), // Gunakan data yang sudah diurutkan
            'pagination' => false,
            'sort' => false, // Matikan sort karena sudah diurutkan manual
        ]);

        return $dataProvider;
    }
}
