<?php

namespace app\models;

use Yii;
use app\traits\AuditTrailTrait;

/**
* This is the model class for table "session".
*
  * @property string $id
  * @property int|null $expire
  * @property resource|null $data
  * @property int|null $user_id
  * @property string|null $last_write
*/
class Session extends \yii\db\ActiveRecord
{
  use AuditTrailTrait;
/**
* {@inheritdoc}
*/
public static function tableName()
{
return 'session';
}

/**
* {@inheritdoc}
*/
public function rules()
{
return [
            [['id'], 'required'],
            [['expire', 'user_id'], 'default', 'value' => null],
            [['expire', 'user_id'], 'integer'],
            [['data', 'last_write'], 'string'],
            [['id'], 'string', 'max' => 40],
            [['id'], 'unique'],
        ];
}

/**
* {@inheritdoc}
*/
public function attributeLabels()
{
return [
  'id' => 'ID',
  'expire' => 'Expire',
  'data' => 'Data',
  'user_id' => 'User ID',
  'last_write' => 'Last Write',
];
}
}
