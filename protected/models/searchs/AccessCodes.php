<?php

namespace app\models\searchs;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\AccessCodes as AccessCodeModel;

/**
 * AccessCodesSearch represents the model behind the search form of `app\models\AccessCodes`.
 */
class AccessCodes extends AccessCodeModel
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_access'], 'integer'],
            [['username', 'password', 'purpose', 'meta'], 'safe'],
            [['is_active'], 'boolean'],
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
        $query = AccessCodes::find();

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
            'id_access' => $this->id_access,
            'is_active' => $this->is_active,
        ]);

        $query->andFilterWhere(['ilike', 'username', $this->username])
            ->andFilterWhere(['ilike', 'password', $this->password])
            ->andFilterWhere(['ilike', 'purpose', $this->purpose])
            ->andFilterWhere(['ilike', 'meta', $this->meta]);

        return $dataProvider;
    }
}
