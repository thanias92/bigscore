<?php

namespace app\modules\task;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Task;

/**
* TaskSearch represents the model behind the search form of `app\models\Task`.
*/
class TaskSearch extends Task
{
/**
* {@inheritdoc}
*/
public $queryString;
public function rules()
{
return [
[['id_task', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['title', 'label_task', 'modul', 'priority_task', 'assign', 'status', 'duedate_task', 'finishdate_task', 'description', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
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
$query = Task::find();

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
            'id_task' => $this->id_task,
            'duedate_task' => $this->duedate_task,
            'finishdate_task' => $this->finishdate_task,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ]);

        $query->andFilterWhere(['ilike', 'title', $this->title])
            ->andFilterWhere(['ilike', 'label_task', $this->label_task])
            ->andFilterWhere(['ilike', 'modul', $this->modul])
            ->andFilterWhere(['ilike', 'priority_task', $this->priority_task])
            ->andFilterWhere(['ilike', 'assign', $this->assign])
            ->andFilterWhere(['ilike', 'status', $this->status])
            ->andFilterWhere(['ilike', 'description', $this->description]);

return $dataProvider;
}
}
