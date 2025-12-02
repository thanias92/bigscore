<?php

namespace app\modules\ticketing;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Roomchat;

/**
* RoomchatSearch represents the model behind the search form of `app\models\Roomchat`.
*/
class RoomchatSearch extends Roomchat
{
/**
* {@inheritdoc}
*/
public $queryString;
public function rules()
{
return [
[['id_chat', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['send', 'chat', 'send_at', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['is_read'], 'boolean'],
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
$query = Roomchat::find();

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
            'id_chat' => $this->id_chat,
            'send_at' => $this->send_at,
            'is_read' => $this->is_read,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ]);

        $query->andFilterWhere(['ilike', 'send', $this->send])
            ->andFilterWhere(['ilike', 'chat', $this->chat]);

return $dataProvider;
}
}
