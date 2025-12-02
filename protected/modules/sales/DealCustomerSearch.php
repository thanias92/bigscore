<?php

namespace app\modules\sales;

use app\models\Deals;
use app\models\Customer;
use yii\data\ArrayDataProvider;
use yii\db\Query;

class DealCustomerSearch
{
    /**
     * @param \app\models\ReportFilterForm $filterModel
     * @return ArrayDataProvider
     */
    public function search($filterModel)
    {
        $query = new Query();
        $query->select([
            'c.customer_name',
            'COUNT(d.deals_id) as total_deals',
            // Menghitung Deals Won secara kondisional
            "SUM(CASE WHEN d.label_deals = 'Deal Won' THEN 1 ELSE 0 END) as deals_won",
            // Menghitung Deals Lost secara kondisional
            "SUM(CASE WHEN d.label_deals = 'Deal Lost' THEN 1 ELSE 0 END) as deals_lost",
            // Menjumlahkan total revenue HANYA dari Deals Won
            "SUM(CASE WHEN d.label_deals = 'Deal Won' THEN CAST(d.total AS numeric) ELSE 0 END) as total_revenue"
        ])
            ->from(['d' => Deals::tableName()])
            ->innerJoin(['c' => Customer::tableName()], 'd.customer_id = c.customer_id')
            ->groupBy('c.customer_name');

        // Terapkan filter tanggal berdasarkan tanggal deal dibuat (created_at)
        if ($filterModel->validate()) {
            $query->andWhere(['between', 'd.created_at', $filterModel->startDate . ' 00:00:00', $filterModel->endDate . ' 23:59:59']);
        }

        $allData = $query->all();

        // Hitung Win Rate di sini menggunakan PHP
        foreach ($allData as $key => $row) {
            if ($row['total_deals'] > 0) {
                $allData[$key]['win_rate'] = ($row['deals_won'] / $row['total_deals']) * 100;
            } else {
                $allData[$key]['win_rate'] = 0;
            }
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $allData,
            'pagination' => false,
            'sort' => [
                'attributes' => ['customer_name', 'total_deals', 'deals_won', 'deals_lost', 'win_rate', 'total_revenue'],
                'defaultOrder' => ['total_revenue' => SORT_DESC], // Urutkan dari customer paling bernilai
            ],
        ]);

        return $dataProvider;
    }
}
