<?php

namespace app\modules\sales;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Customer;

/**
 * CustomerSearch represents the model behind the search form of `app\models\Customer`.
 */
class CustomerSearch extends Customer
{
    /**
     * {@inheritdoc}
     */
    public $queryString;
    public function rules()
    {
        return [
            [['customer_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['customer_name', 'customer_email', 'customer_phone', 'customer_address', 'customer_website', 'establishment_date', 'customer_source', 'pic_name', 'pic_email', 'pic_phone', 'pic_workroles', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
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
        $query = Customer::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
        $queryString = isset($params['CustomerSearch']['queryString']) ? $params['CustomerSearch']['queryString'] : '';
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'customer_id' => $this->customer_id,
            'establishment_date' => $this->establishment_date,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ]);

        $query->andFilterWhere(['ilike', 'customer_name', $queryString])
            ->andFilterWhere(['ilike', 'customer_email', $this->customer_email])
            ->andFilterWhere(['ilike', 'customer_phone', $this->customer_phone])
            ->andFilterWhere(['ilike', 'customer_address', $this->customer_address])
            ->andFilterWhere(['ilike', 'customer_website', $this->customer_website])
            ->andFilterWhere(['ilike', 'customer_source', $this->customer_source])
            ->andFilterWhere(['ilike', 'pic_name', $this->pic_name])
            ->andFilterWhere(['ilike', 'pic_email', $this->pic_email])
            ->andFilterWhere(['ilike', 'pic_phone', $this->pic_phone])
            ->andFilterWhere(['ilike', 'pic_workroles', $this->pic_workroles]);

        return $dataProvider;
    }
}
