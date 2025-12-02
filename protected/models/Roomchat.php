<?php

namespace app\models;

use Yii;
use app\traits\AuditTrailTrait;

/**
 * This is the model class for table "roomchat".
 *
 * @property int $id_chat
 * @property int|null $id_customer
 * @property int|null $id_staff
 * @property string $chat
 * @property string $send_at
 * @property bool|null $is_read
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 */
class Roomchat extends \yii\db\ActiveRecord
{
  use AuditTrailTrait;
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'roomchat';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['id_customer', 'id_staff', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
      [['chat', 'send_at'], 'required'],
      [['send_at', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
      [['is_read'], 'boolean'],
      [['chat'], 'string', 'max' => 255],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id_chat' => 'ID Chat',
      'id_customer' => 'ID Customer',
      'id_staff' => 'ID Staff',
      'chat' => 'Isi Pesan',
      'send_at' => 'Waktu Kirim',
      'is_read' => 'Sudah Dibaca?',
      'created_by' => 'Dibuat Oleh',
      'updated_by' => 'Diperbarui Oleh',
      'deleted_by' => 'Dihapus Oleh',
      'created_at' => 'Dibuat Pada',
      'updated_at' => 'Diperbarui Pada',
      'deleted_at' => 'Dihapus Pada',
    ];
  }
  // Opsional: relasi ke customer dan staff jika ada modelnya
  public function getCustomer()
  {
    return $this->hasOne(Customer::class, ['customer_id' => 'id_customer']);
  }

  public function getStaff()
  {
    return $this->hasOne(Staff::class, ['id_staff' => 'id_staff']);
  }
}
