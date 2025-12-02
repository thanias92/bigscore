<?php

namespace app\modules\sales;

use app\models\Deals;
use yii\data\ActiveDataProvider;

class DealWonReportSearch
{
    /**
     * @param \app\models\ReportFilterForm $filterModel
     * @return ActiveDataProvider
     */
    public function search($filterModel)
    {
        // Mulai query dari model Deals
        $query = Deals::find();

        // Gabungkan dengan relasi customer dan product agar bisa ditampilkan
        $query->joinWith(['customer', 'product']);

        // INI ADALAH KUNCI UTAMA: Filter hanya untuk deal yang sudah menang
        $query->where(['deals.label_deals' => 'Deal Won']);

        // Terapkan filter tanggal berdasarkan tanggal pembelian (purchase_date)
        if ($filterModel->validate()) {
            $query->andWhere(['between', 'deals.purchase_date', $filterModel->startDate, $filterModel->endDate]);
        }

        // Gunakan ActiveDataProvider karena kita menampilkan daftar record
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20, // Tampilkan 20 data per halaman
            ],
            'sort' => [
                // Izinkan sorting berdasarkan kolom relasi
                'attributes' => [
                    'purchase_date',
                    'deals_code',
                    'customer.customer_name',
                    'product.product_name',
                    'total',
                ],
                'defaultOrder' => ['purchase_date' => SORT_DESC], // Urutkan dari yang terbaru
            ],
        ]);

        return $dataProvider;
    }
}
