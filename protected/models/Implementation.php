<?php

namespace app\models;

use Yii;
use app\traits\AuditTrailTrait;


/**
 * This is the model class for table "implementation".
 *
 * @property int $id_implementasi
 * @property string $activity_title
 * @property string $activity
 * @property string $detail
 * @property string $start_date
 * @property string|null $completion_date
 * @property string $pic_aktivitas
 * @property string $status
 * @property string|null $notes
 * @property string|null $duration
 * @property int|null $line_progress
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 */
class Implementation extends \yii\db\ActiveRecord
{
  public $label_deals;
  public $customer_name;

  use AuditTrailTrait;
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'implementation';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      // [['completion_date', 'notes', 'duration', 'created_by', 'updated_by', 'deleted_by', 'deleted_at', 'label_deals', 'nama_customer'], 'default', 'value' => null],
      // [['line_progress'], 'default', 'value' => 0],
      // [['activity_title', 'activity', 'detail', 'start_date', 'pic_aktivitas', 'status'], 'required'],
      // [['start_date', 'completion_date', 'duration', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
      // [['notes'], 'string'],
      // [['line_progress', 'created_by', 'updated_by', 'deleted_by'], 'default', 'value' => null],
      // [['line_progress', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
      // [['activity_title', 'activity', 'detail', 'pic_aktivitas', 'status'], 'string', 'max' => 255],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [];
  }

  public function getDeals()
  {
    return $this->hasOne(Deals::class, ['deals_id' => 'deals_id']);
  }

  public function getImplementationDeail()
  {
    return $this->hasOne(ImplementationDetail::class, ['id_implementasi' => 'id_implementasi']);
  }
}
