<?php

namespace app\models;

use Yii;
use app\traits\AuditTrailTrait;

/**
* This is the model class for table "migration".
*
  * @property string $version
  * @property int|null $apply_time
*/
class Migration extends \yii\db\ActiveRecord
{
  use AuditTrailTrait;
/**
* {@inheritdoc}
*/
public static function tableName()
{
return 'migration';
}

/**
* {@inheritdoc}
*/
public function rules()
{
return [
            [['version'], 'required'],
            [['apply_time'], 'default', 'value' => null],
            [['apply_time'], 'integer'],
            [['version'], 'string', 'max' => 180],
            [['version'], 'unique'],
        ];
}

/**
* {@inheritdoc}
*/
public function attributeLabels()
{
return [
  'version' => 'Version',
  'apply_time' => 'Apply Time',
];
}
}
