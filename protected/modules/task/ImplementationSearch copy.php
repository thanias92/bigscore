<?php

namespace app\modules\task;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Implementation;

/**
* ImplementationSearch represents the model behind the search form of `app\models\Implementation`.
*/
class ImplementationSearch extends Implementation
{
/**
* {@inheritdoc}
*/
public $queryString;
public function rules()
{
return [
[['id_implementasi', 'line_progress', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['activity_title', 'activity', 'detail', 'start_date', 'completion_date', 'pic_aktivitas', 'status', 'notes', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
['queryString','safe']
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
$query = Implementation::find();

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
            'id_implementasi' => $this->id_implementasi,
            'start_date' => $this->start_date,
            'completion_date' => $this->completion_date,
            'line_progress' => $this->line_progress,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ]);

        $query->andFilterWhere(['ilike', 'activity_title', $this->activity_title])
            ->andFilterWhere(['ilike', 'activity', $this->activity])
            ->andFilterWhere(['ilike', 'detail', $this->detail])
            ->andFilterWhere(['ilike', 'pic_aktivitas', $this->pic_aktivitas])
            ->andFilterWhere(['ilike', 'status', $this->status])
            ->andFilterWhere(['ilike', 'notes', $this->notes]);

return $dataProvider;
}
}
