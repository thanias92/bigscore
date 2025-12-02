<?php

namespace app\modules\sales;

use app\models\Deals;
use app\models\Product;
use yii\data\ArrayDataProvider;
use yii\db\Query;

class ProductSalesReportSearch
{
    /**
     * @param \app\models\ReportFilterForm $filterModel
     * @return ArrayDataProvider
     */
    public function search($filterModel)
    {
        $query = new Query();
        $query->select([
            'p.product_name',
            'SUM(d.unit_product) as total_units_sold',
            'SUM(CAST(d.total AS numeric)) as total_revenue'
        ])
            ->from(['d' => Deals::tableName()])
            ->innerJoin(['p' => Product::tableName()], 'd.product_id = p.id_produk')
            ->where(['d.label_deals' => 'Deal Won']) // PENTING: Hanya hitung deal yang sudah menang
            ->groupBy('p.product_name');

        // Terapkan filter tanggal jika valid
        if ($filterModel->validate()) {
            // Gunakan purchase_date untuk laporan penjualan, bukan created_at
            $query->andWhere(['between', 'd.purchase_date', $filterModel->startDate, $filterModel->endDate]);
        }

        $allData = $query->all();

        $dataProvider = new ArrayDataProvider([
            'allModels' => $allData,
            'pagination' => false,
            'sort' => [
                'attributes' => ['product_name', 'total_units_sold', 'total_revenue'],
                'defaultOrder' => ['total_revenue' => SORT_DESC], // Urutkan dari pendapatan terbesar
            ],
        ]);

        return $dataProvider;
    }
}
