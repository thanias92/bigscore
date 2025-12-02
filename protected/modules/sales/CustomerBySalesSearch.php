<?php

namespace app\modules\sales;

use app\models\Customer;
use yii\data\ActiveDataProvider;

class CustomerBySalesSearch
{
    /**
     * @param \app\models\ReportFilterForm $filterModel
     * @return ActiveDataProvider
     */
    public function search($filterModel)
    {
        // 1. Dapatkan semua ID user yang memiliki peran sales
        $auth = \Yii::$app->authManager;
        $salesManagerIDs = $auth->getUserIdsByRole('Sales Manager');
        $salesRepIDs = $auth->getUserIdsByRole('Sales Representative');
        $salesmenIDs = array_merge($salesManagerIDs, $salesRepIDs);

        if (empty($salesmenIDs)) {
            $salesmenIDs = [0];
        }

        // 2. Query utama sekarang ke tabel Customer
        $query = Customer::find()
            ->joinWith('createdBy user') // Eager load data salesman
            ->where(['in', 'customer.created_by', $salesmenIDs]);

        // 3. Terapkan filter tanggal ke tanggal customer dibuat (created_at)
        if ($filterModel->validate()) {
            // Gunakan created_at dari tabel customer
            $query->andWhere(['between', 'customer.created_at', $filterModel->startDate, $filterModel->endDate]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50, // Tampilkan lebih banyak customer
            ],
            'sort' => [
                'attributes' => [
                    'customer_name',
                    'created_at',
                    'createdBy.username' => [ // Atribut untuk sorting berdasarkan nama sales
                        'asc' => ['user.username' => SORT_ASC],
                        'desc' => ['user.username' => SORT_DESC],
                        'label' => 'Salesperson'
                    ],
                ],
                'defaultOrder' => [
                    'createdBy.username' => SORT_ASC,
                    'created_at' => SORT_DESC
                ],  
            ],
        ]);

        return $dataProvider;
    }
}
