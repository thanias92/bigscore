<?php

namespace app\modules\vendorfinance;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Product;

/**
 * ProductSearch represents the model behind the search form of `app\models\Product`.
 */
class ProductSearch extends Product
{
    /**
     * {@inheritdoc}
     */
    public $queryString;
    public function rules()
    {
        return [
            [['id_produk', 'unit', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['no_produk', 'code_produk', 'keterangan', 'product_name', 'harga', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
            ['queryString', 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Product::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
        $queryString = isset($params['ProductSearch']['queryString']) ? $params['ProductSearch']['queryString'] : '';
        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id_produk' => $this->id_produk,
            'unit' => $this->unit,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ]);

        if (!empty($this->queryString)) {
            $query->andFilterWhere([
                'or',
                ['ilike', 'product_name', $this->queryString],
                ['ilike', 'code_produk', $this->queryString],
                ['ilike', 'no_produk', $this->queryString],
                ['ilike', 'harga', $this->queryString],
            ]);
        }

        return $dataProvider;
    }
}
