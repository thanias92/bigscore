<?php

namespace app\modules\task;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Ticket;

/**
 * TicketSearch represents the model behind the search form of `app\models\Ticket`.
 */
class TicketSearch extends Ticket
{
    /**
     * {@inheritdoc}
     */
    public $queryString;
    public function rules()
    {
        return [
            [['id_ticket', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['code_ticket', 'priority_ticket', 'label_ticket', 'via', 'assigne', 'modul', 'title', 'date_ticket', 'status_ticket', 'description', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
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
        $query = Ticket::find();

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
            'id_ticket' => $this->id_ticket,
            'date_ticket' => $this->date_ticket,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ]);

        $query->andFilterWhere(['ilike', 'code_ticket', $this->code_ticket])
            ->andFilterWhere(['ilike', 'priority_ticket', $this->priority_ticket])
            ->andFilterWhere(['ilike', 'label_ticket', $this->label_ticket])
            ->andFilterWhere(['ilike', 'via', $this->via])
            ->andFilterWhere(['ilike', 'assigne', $this->assigne])
            ->andFilterWhere(['ilike', 'modul', $this->modul])
            ->andFilterWhere(['ilike', 'title', $this->title])
            ->andFilterWhere(['ilike', 'status_ticket', $this->status_ticket])
            ->andFilterWhere(['ilike', 'description', $this->description]);

        return $dataProvider;
    }
}
