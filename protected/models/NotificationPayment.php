<?php

namespace app\models;

use Yii;
use app\traits\AuditTrailTrait;

/**
 * This is the model class for table "notification_payment".
 *
 * @property int $id_notification_payment
 * @property int $id_pemasukan // id dari pemasukan
 * @property string $status_payment_notification
 * @property string $date_notificatian
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 *
 * @property Pemasukan $pemasukan
 */
class NotificationPayment extends \yii\db\ActiveRecord
{
  use AuditTrailTrait;

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'notification_payment';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['id_pemasukan', 'status_payment_notification', 'date_notificatian'], 'required'],
      [['id_pemasukan', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
      [['date_notificatian', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
      [['status_payment_notification'], 'string', 'max' => 255],
      [['id_pemasukan'], 'exist', 'skipOnError' => true, 'targetClass' => Pemasukan::class, 'targetAttribute' => ['id_pemasukan' => 'pemasukan_id']],

    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id_notification_payment' => 'ID Notifikasi Pembayaran',
      'id_pemasukan' => 'ID Pemasukan ',
      'status_payment_notification' => 'Status Notifikasi Pembayaran',
      'date_notificatian' => 'Tanggal Notifikasi',
      'created_by' => 'Dibuat Oleh',
      'updated_by' => 'Diperbarui Oleh',
      'deleted_by' => 'Dihapus Oleh',
      'created_at' => 'Dibuat Pada',
      'updated_at' => 'Diperbarui Pada',
      'deleted_at' => 'Dihapus Pada',
    ];
  }

  /**
   * Gets query for [[PemasukanCicilan]].
   *
   * @return \yii\db\ActiveQuery
   */
  public function getPemasukan()
  {
    return $this->hasOne(Pemasukan::class, ['pemasukan_id' => 'id_pemasukan']);
  }
  public static function data_notifpayment_all_detail($filter = [])
{
    $query = self::find()
        ->with(['pemasukan.deals.customer']) // relasi berantai sampai customer
        ->where(['notification_payment.deleted_at' => null]);

    if (!empty($filter)) {
        $query->andFilterWhere($filter);
    }

    $notifPayments = $query->all();
    $list = [];

    foreach ($notifPayments as $notif) {
        $list[$notif->id_notification_payment] = [
            'id_notification_payment' => $notif->id_notification_payment,
            'nama_customer' => $notif->pemasukan->deals->customer->customer_name ?? '-', // relasi: pemasukan → deals → customer
            'tanggal' => Yii::$app->formatter->asDate($notif->date_notificatian),
            'status' => $notif->status_payment_notification ?? '-',
            'total_pemasukan' => Yii::$app->formatter->asCurrency($notif->pemasukan->jumlah ?? 0), // asumsi ada `jumlah` di tabel pemasukan
        ];
    }

    return $list;
}

}
