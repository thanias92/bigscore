<?php

namespace app\models;

use Yii;
use app\traits\AuditTrailTrait;

/**
* This is the model class for table "sub_task".
*
  * @property int $id_subtask
  * @property string $title_subtask
  * @property string|null $item_subtask
  * @property string|null $progress_subtask
  * @property int|null $created_by
  * @property int|null $updated_by
  * @property int|null $deleted_by
  * @property string|null $created_at
  * @property string|null $updated_at
  * @property string|null $deleted_at
*/
class SubTask extends \yii\db\ActiveRecord
{
  use AuditTrailTrait;
/**
* {@inheritdoc}
*/
public static function tableName()
{
return 'sub_task';
}

/**
* {@inheritdoc}
*/
public function rules()
{
return [
            [['item_subtask', 'progress_subtask', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at'], 'default', 'value' => null],
            [['title_subtask'], 'required'],
            [['created_by', 'updated_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['title_subtask', 'item_subtask', 'progress_subtask'], 'string', 'max' => 255],
        ];
}

/**
* {@inheritdoc}
*/
public function attributeLabels()
{
return [
  'id_subtask' => 'Id Subtask',
  'title_subtask' => 'Title Subtask',
  'item_subtask' => 'Item Subtask',
  'progress_subtask' => 'Progress Subtask',
  'created_by' => 'Created By',
  'updated_by' => 'Updated By',
  'deleted_by' => 'Deleted By',
  'created_at' => 'Created At',
  'updated_at' => 'Updated At',
  'deleted_at' => 'Deleted At',
];
}

  public function getItemSubtasks()
  {
      return $this->hasMany(ItemSubtask::class, ['id_subtask' => 'id_subtask']);
  }

}
