<?php

namespace app\traits;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

trait AuditTrailTrait
{
  public function behaviors()
  {
    return [
      'blameable' => [
        'class' => BlameableBehavior::class,
        'createdByAttribute' => 'created_by',
        'updatedByAttribute' => 'updated_by',
        'value' => Yii::$app->user->id ?: null
      ],
      'timestamp' => [
        'class' => TimestampBehavior::class,
        'createdAtAttribute' => 'created_at',
        'updatedAtAttribute' => 'updated_at',
        'value' => date('Y-m-d H:i:s'),
      ],
    ];
  }

  public static function find()
  {
    return parent::find()->where([static::tableName().'.deleted_at' => null]);
  }

  public function delete()
  {
    if ($this->beforeDelete()) {
      $this->deleted_at = date('Y-m-d H:i:s');
      $this->deleted_by = Yii::$app->user->id;
      $this->update(false, ['deleted_at', 'deleted_by']);
      $this->afterDelete();
      return true;
    } else {
      return false;
    }
  }
}
