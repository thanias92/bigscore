<?php

namespace app\modules\ticketing;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Ticket;

/**
 * TicketingSearch represents the model behind the search form of `app\models\Ticket`.
 */
class TicketingSearch extends Ticket
{
    public $queryString;
    public $start_date;
    public $end_date;


    public function rules()
    {
        return [
            [['id_ticket', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['code_ticket', 'role', 'priority_ticket', 'label_ticket', 'via', 'assigne', 'modul', 'title', 'date_ticket', 'status_ticket', 'description', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['queryString', 'start_date', 'end_date'], 'safe']
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Ticket::find()
            ->joinWith(['deals.customer']) // cukup relasi yang memang ada
            ->where(['ticket.deleted_at' => null]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'ticket.id_ticket' => $this->id_ticket,
            'ticket.created_by' => $this->created_by,
            'ticket.updated_by' => $this->updated_by,
            'ticket.deleted_by' => $this->deleted_by,
            'ticket.created_at' => $this->created_at,
            'ticket.updated_at' => $this->updated_at,
            'ticket.deleted_at' => $this->deleted_at,
        ]);

        $query->andFilterWhere(['ilike', 'ticket.code_ticket', $this->code_ticket])
            ->andFilterWhere(['ilike', 'ticket.role', $this->role])
            ->andFilterWhere(['ilike', 'ticket.priority_ticket', $this->priority_ticket])
            ->andFilterWhere(['ilike', 'ticket.label_ticket', $this->label_ticket])
            ->andFilterWhere(['ilike', 'ticket.via', $this->via])
            ->andFilterWhere(['ilike', 'ticket.date_ticket', $this->date_ticket])
            ->andFilterWhere(['ilike', 'ticket.assigne', $this->assigne])
            ->andFilterWhere(['ilike', 'ticket.modul', $this->modul])
            ->andFilterWhere(['ilike', 'ticket.title', $this->title])
            ->andFilterWhere(['ilike', 'ticket.status_ticket', $this->status_ticket])
            ->andFilterWhere(['ilike', 'ticket.description', $this->description]);

        // Pencarian gabungan (queryString)
        if (!empty($this->queryString)) {
            $query->andWhere([
                'or',
                ['ilike', 'ticket.code_ticket', $this->queryString],
                ['ilike', 'ticket.priority_ticket', $this->queryString],
                ['ilike', 'ticket.label_ticket', $this->queryString],
                ['ilike', 'ticket.status_ticket', $this->queryString],
                ['ilike', 'ticket.modul', $this->queryString],
                ['ilike', 'ticket.title', $this->queryString],
                ['ilike', 'ticket.description', $this->queryString],
                ['ilike', 'customer.customer_name', $this->queryString], // pastikan relasi deals->customer->nama tersedia
            ]);
        }

        if ($this->start_date && $this->end_date) {
            $query->andFilterWhere(['between', 'date_ticket', $this->start_date, $this->end_date]);
        }

        return $dataProvider;
    }
}
