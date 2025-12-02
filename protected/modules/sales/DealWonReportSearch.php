<?php

namespace app\modules\sales;

use app\models\Deals;
use yii\data\ActiveDataProvider;

class DealWonReportSearch
{
    public function search($filterModel)
    {
        $query = Deals::find();

        // --- PERBAIKAN: Tambahkan relasi createdBy ---
        $query->joinWith(['customer', 'product', 'createdBy']);

        $query->where(['deals.label_deals' => 'Deal Won']);

        if ($filterModel->validate()) {
            $query->andWhere(['between', 'deals.purchase_date', $filterModel->startDate, $filterModel->endDate]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'attributes' => [
                    'purchase_date',
                    'deals_code',
                    'customer.customer_name',
                    'product.product_name',
                    'total',
                    'createdBy.username', // Tambahkan agar bisa di-sort
                ],
                'defaultOrder' => ['purchase_date' => SORT_DESC],
            ],
        ]);

        return $dataProvider;
    }
}
