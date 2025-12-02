<?php

namespace app\models;

use Yii;
use app\traits\AuditTrailTrait;

/**
 * This is the model class for table "feedback".
 *
 * @property int $id_feedback
 * @property string $date_feedback
 * @property string $feedback
 * @property string $rate
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 */
class Feedback extends \yii\db\ActiveRecord
{
  use AuditTrailTrait;
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'feedback';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['created_by', 'updated_by', 'deleted_by', 'deleted_at'], 'default', 'value' => null],
      [['id_deals', 'date_feedback', 'feedback', 'rate'], 'required'],
      [['date_feedback', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
      [['created_by', 'updated_by', 'deleted_by'], 'default', 'value' => null],
      [['id_deals', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
      [['feedback', 'rate'], 'string', 'max' => 255],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id_feedback' => 'Id Feedback',
      'id_deals' => 'Nama Customer',
      'date_feedback' => 'Date Feedback',
      'feedback' => 'Feedback',
      'rate' => 'Rate',
      'created_by' => 'Created By',
      'updated_by' => 'Updated By',
      'deleted_by' => 'Deleted By',
      'created_at' => 'Created At',
      'updated_at' => 'Updated At',
      'deleted_at' => 'Deleted At'
    ];
  }
  public function getDeals()
  {
    return $this->hasOne(Deals::class, ['deals_id' => 'id_deals']);
  }
  public static function data_feedback_all_detail($filter = [])
  {
    $query = self::find()
      ->with(['deals']) // relasi untuk mendapatkan nama customer
      ->where(['deleted_at' => null]);

    if (!empty($filter)) {
      $query->andFilterWhere($filter);
    }

    $feedbacks = $query->all();
    $list = [];

    foreach ($feedbacks as $feedback) {
      $list[$feedback->id_feedback] = [
        'id_feedback'   => $feedback->id_feedback,
        'nama_customer' => $feedback->deals->customer->customer_name ?? '-', // relasi deals → customer → customer_name
        'tanggal'       => Yii::$app->formatter->asDate($feedback->date_feedback),
        'rate'          => $feedback->rate ?? '-',
        'feedback'      => $feedback->feedback ?? '-',
      ];
    }

    return $list;
  }
}