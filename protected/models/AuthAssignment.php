<?php

namespace app\models;

use Yii;
use app\traits\AuditTrailTrait;

/**
 * This is the model class for table "auth_assignment".
 *
 * @property string $item_name
 * @property string $user_id
 * @property int|null $created_at
 */
class AuthAssignment extends \yii\db\ActiveRecord
{
  // use AuditTrailTrait;
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'auth_assignment';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['item_name', 'user_id'], 'required'],
      [['created_at', 'deleted_at'], 'safe'],
      [['item_name', 'user_id'], 'string', 'max' => 255],
      [['item_name', 'user_id'], 'unique', 'targetAttribute' => ['item_name', 'user_id']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'item_name' => 'Item Name',
      'user_id' => 'User ID',
      'created_at' => 'Created At',
    ];
  }
}
