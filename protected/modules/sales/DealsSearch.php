<?php

namespace app\modules\sales;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Deals; // Pastikan namespace ini benar

/**
 * DealsSearch represents the model behind the search form of `app\models\Deals`.
 */
class DealsSearch extends Deals
{
    public $queryString;

    public function rules()
    {
        return [
            [['deals_id', 'customer_id', 'product_id', 'price_product', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            // Hapus 'history' dari sini
            [['total', 'label_deals', 'purchase_type', 'purchase_date', 'description', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
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
        $query = Deals::find()->with('createdBy');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC] // Urutkan dari yang terbaru
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'deals_id' => $this->deals_id,
            'customer_id' => $this->customer_id,
            'product_id' => $this->product_id,
            'price_product' => $this->price_product,
            'purchase_type' => $this->purchase_type,
            'purchase_date' => $this->purchase_date,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ]);

        $query->andFilterWhere(['ilike', 'total', $this->total])
            ->andFilterWhere(['ilike', 'label_deals', $this->label_deals])
            ->andFilterWhere(['ilike', 'description', $this->description]);

        return $dataProvider;
    }
}
