<?php

namespace app\modules\sales;

use app\models\Deals;
use app\models\User;
use yii\data\ActiveDataProvider;

class DealsByRepresentativeSearch
{
    /**
     * @param \app\models\ReportFilterForm $filterModel
     * @return ActiveDataProvider
     */
    public function search($filterModel)
    {
        // --- PERBAIKAN: Dapatkan ID HANYA dari user yang memiliki role "Sales Representative" ---
        $auth = \Yii::$app->authManager;
        $representativeIDs = $auth->getUserIdsByRole('Sales Representative');

        // Jika tidak ada user dengan role tersebut, kembalikan data kosong
        if (empty($representativeIDs)) {
            $representativeIDs = [0]; // Beri nilai dummy agar query tidak error
        }

        // Query utama untuk mengambil data Deals
        $query = Deals::find()
            ->joinWith(['customer', 'product', 'createdBy']); // Gunakan relasi asli

        // Filter berdasarkan daftar ID Sales Representative yang sudah didapatkan
        $query->where(['in', 'deals.created_by', $representativeIDs]);

        // Terapkan filter tanggal dari form
        if ($filterModel->validate()) {
            // Ganti 'deals.purchase_date' menjadi 'deals.created_at'
            $query->andWhere(['between', 'deals.created_at', $filterModel->startDate, $filterModel->endDate]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'attributes' => [
                    'purchase_date',
                    'total',
                    'customer.customer_name',
                    'createdBy.username' => [
                        'asc' => ['user.username' => SORT_ASC],
                        'desc' => ['user.username' => SORT_DESC],
                        'label' => 'Salesperson'
                    ],
                ],
                'defaultOrder' => ['purchase_date' => SORT_DESC],
            ],
        ]);

        return $dataProvider;
    }
}
