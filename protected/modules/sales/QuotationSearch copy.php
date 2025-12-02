<?php

namespace app\modules\sales;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Quotation;

/**
 * QuotationSearch represents the model behind the search form of `app\models\Quotation`.
 */
class QuotationSearch extends Quotation
{
    /**
     * {@inheritdoc}
     */
    public $queryString;
    public function rules()
    {
        return [
            [['quotation_id', 'customer_id', 'product_id', 'unit_product', 'price_product', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['quotation_code', 'total', 'quotation_file', 'created_date', 'expiration_date', 'description', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
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
        $query = Quotation::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'quotation_id' => $this->quotation_id,
            'customer_id' => $this->customer_id,
            'product_id' => $this->product_id,
            'unit_product' => $this->unit_product,
            'price_product' => $this->price_product,
            'created_date' => $this->created_date,
            'expiration_date' => $this->expiration_date,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ]);

        $query->andFilterWhere(['ilike', 'quotation_code', $this->quotation_code])
            ->andFilterWhere(['ilike', 'total', $this->total])
            ->andFilterWhere(['ilike', 'description', $this->description]);

        return $dataProvider;
    }
}
