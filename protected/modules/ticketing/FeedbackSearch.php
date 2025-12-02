<?php

namespace app\modules\ticketing;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Feedback;

/**
 * FeedbackSearch represents the model behind the search form of app\models\Feedback.
 */
class FeedbackSearch extends Feedback
{
    public $queryString;
    public $start_date;
    public $end_date;

    public function rules()
    {
        return [
            [['id_feedback', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['date_feedback', 'feedback', 'rate', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['queryString', 'start_date', 'end_date'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Feedback::find()
            ->joinWith(['deals.customer', 'deals.product']) // join relasi deals -> customer & product
            ->where(['feedback.deleted_at' => null]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if ($this->start_date && $this->end_date) {
            $query->andFilterWhere(['between', 'date_feedback', $this->start_date, $this->end_date]);
        }

        // Filter spesifik kolom
        $query->andFilterWhere([
            'feedback.id_feedback' => $this->id_feedback,
            'feedback.date_feedback' => $this->date_feedback,
            'feedback.created_by' => $this->created_by,
            'feedback.updated_by' => $this->updated_by,
            'feedback.deleted_by' => $this->deleted_by,
            'feedback.created_at' => $this->created_at,
            'feedback.updated_at' => $this->updated_at,
            'feedback.deleted_at' => $this->deleted_at,
        ]);

        // Filter kolom ILIKE (kasus-insensitive partial match)
        $query->andFilterWhere(['ilike', 'feedback.feedback', $this->feedback])
            ->andFilterWhere(['ilike', 'feedback.rate', $this->rate]);

        // Pencarian gabungan query string
        if (!empty($this->queryString)) {
            $query->andWhere([
                'or',
                // ['ilike', 'feedback.feedback', $this->queryString],
                ['ilike', 'feedback.rate', $this->queryString],
                ['ilike', 'customer.customer_name', $this->queryString],
                ['ilike', 'product.product_name', $this->queryString],
            ]);
        }

        return $dataProvider;
    }
}