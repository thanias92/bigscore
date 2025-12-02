<?php

namespace app\modules\sales;

use app\models\Deals;
use app\models\Customer;
use app\models\Product;
use app\models\User;
use yii\data\ArrayDataProvider;
use yii\db\Query; // PENTING: Gunakan Query Builder

class CustomerBySalesSearch
{
    /**
     * @param \app\models\ReportFilterForm $filterModel
     * @return ArrayDataProvider
     */
    public function search($filterModel)
    {
        // Ganti dari ActiveQuery (Deals::find()) menjadi Query Builder (new Query())
        $query = new Query();

        $query->select([
            'd.deals_id',
            'c.customer_id',
            'c.customer_name',
            'p.product_name',
            'd.purchase_date',
            'd.total',
            's.username as salesperson_name'
        ])
            ->from(['d' => Deals::tableName()])
            // Ganti joinWith menjadi innerJoin untuk hasil yang lebih bersih
            ->innerJoin(['c' => Customer::tableName()], 'c.customer_id = d.customer_id')
            ->innerJoin(['p' => Product::tableName()], 'p.id_produk = d.product_id')
            ->innerJoin(['s' => User::tableName()], 's.id = d.created_by')
            ->where(['d.label_deals' => 'Deal Won'])
            ->andWhere(['in', 'd.created_by', [5, 6]]);


        if ($filterModel->validate()) {
            $query->andWhere(['between', 'd.purchase_date', $filterModel->startDate, $filterModel->endDate]);
        }

        $allData = $query->all();

        $dataProvider = new ArrayDataProvider([
            'allModels' => $allData,
            'pagination' => false,
            'sort' => [
                'attributes' => [
                    'salesperson_name',
                    'purchase_date',
                    'total',
                    'customer_name',
                    'product_name'
                ],
                'defaultOrder' => ['salesperson_name' => SORT_ASC, 'purchase_date' => SORT_DESC],
            ],
        ]);

        return $dataProvider;
    }
}
