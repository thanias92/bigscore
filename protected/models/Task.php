<?php

namespace app\models;

use Yii;
use app\traits\AuditTrailTrait;

/**
* This is the model class for table "task".
*
  * @property int $id_task
  * @property string $title
  * @property string|null $label_task
  * @property string|null $modul
  * @property string|null $priority_task
  * @property string|null $assign
  * @property string|null $status
  * @property string|null $duedate_task
  * @property string|null $finishdate_task
  * @property string|null $description
  * @property int|null $created_by
  * @property int|null $updated_by
  * @property int|null $deleted_by
  * @property string|null $created_at
  * @property string|null $updated_at
  * @property string|null $deleted_at
*/
class Task extends \yii\db\ActiveRecord
{
  use AuditTrailTrait;
/**
* {@inheritdoc}
*/
public static function tableName()
{
return 'task';
}

/**
* {@inheritdoc}
*/

public function rules()
{
    return [
        [['label_task', 'modul', 'priority_task', 'assign', 'status', 'duedate_task', 'finishdate_task', 'description', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at'], 'default', 'value' => null],

        [['title'], 'required'], 

        [['customer_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],

        [['duedate_task', 'finishdate_task', 'created_at', 'updated_at', 'deleted_at'], 'safe'],

        [['description'], 'string'],

        [['title', 'modul', 'priority_task', 'assign', 'status'], 'string', 'max' => 255],

        [['label_task'], 'each', 'rule' => ['string']],
    ];
}


public function beforeSave($insert)
{
    if (is_array($this->label_task)) {
        $this->label_task = json_encode($this->label_task);
    }
    return parent::beforeSave($insert);
}

public function afterFind()
{
    if (!empty($this->label_task) && is_string($this->label_task)) {
        $this->label_task = json_decode($this->label_task, true); 
    }
    parent::afterFind();
}


/**
* {@inheritdoc}
*/
public function attributeLabels()
{
return [
  'id_task' => 'Id Task',
  'title' => 'Title',
  'label_task' => 'Label Task',
  'modul' => 'Modul',
  'priority_task' => 'Priority Task',
  'customer_id' => 'Customer ID',
  'id_ticket',
  'assign' => 'Assign',
  'status' => 'Status',
  'duedate_task' => 'Duedate Task',
  'finishdate_task' => 'Finishdate Task',
  'description' => 'Description',
  'created_by' => 'Created By',
  'updated_by' => 'Updated By',
  'deleted_by' => 'Deleted By',
  'created_at' => 'Created At',
  'updated_at' => 'Updated At',
  'deleted_at' => 'Deleted At',
];
}
}
