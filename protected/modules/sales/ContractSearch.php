<?php

namespace app\modules\sales;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Contract;

class ContractSearch extends Contract
{
    public $queryString;

    public function rules()
    {
        return [
            ['queryString', 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Contract::find();

        // PENTING: Gabungkan dengan relasi 'customer' dan 'product'
        // Yii2 akan otomatis menangani join melalui tabel perantara (pemasukan, deals)
        $query->joinWith(['customer', 'product']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['contract_id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // Terapkan filter pencarian ke beberapa kolom
        $query->andFilterWhere([
            'or',
            ['ilike', 'contract.contract_code', $this->queryString],
            ['ilike', 'customer.customer_name', $this->queryString],
            ['ilike', 'product.product_name', $this->queryString],
            ['ilike', 'contract.status_contract', $this->queryString]
        ]);

        return $dataProvider;
    }
}
