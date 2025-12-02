<?php

namespace app\models;

use Yii;
use app\traits\AuditTrailTrait;

/**
 * This is the model class for table "notification_contract".
 *
 * @property int $id_notification_contract
 * @property int $contract_id
 * @property string $status_contract_notification
 * @property string $date_notificatian_contract
 * @property string|null $description
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 *
 * @property Contract $contract
 */
class NotificationContract extends \yii\db\ActiveRecord
{
  use AuditTrailTrait;
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'notification_contract';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['description', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at'], 'default', 'value' => null],
      [['contract_id', 'status_contract_notification', 'date_notificatian_contract'], 'required'],
      [['contract_id', 'created_by', 'updated_by', 'deleted_by'], 'default', 'value' => null],
      [['contract_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
      [['date_notificatian_contract', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
      [['description'], 'string'],
      [['status_contract_notification'], 'string', 'max' => 255],
      [['contract_id'], 'exist', 'skipOnError' => true, 'targetClass' => Contract::class, 'targetAttribute' => ['contract_id' => 'contract_id']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id_notification_contract' => 'Id Notification Contract',
      'contract_id' => 'Contract ID',
      'status_contract_notification' => 'Status Contract Notification',
      'date_notificatian_contract' => 'Date Notificatian Contract',
      'description' => 'Description',
      'created_by' => 'Created By',
      'updated_by' => 'Updated By',
      'deleted_by' => 'Deleted By',
      'created_at' => 'Created At',
      'updated_at' => 'Updated At',
      'deleted_at' => 'Deleted At',
    ];
  }

  /**
   * Gets query for [[Contract]].
   *
   * @return \yii\db\ActiveQuery
   */
  public function getContract()
  {
    return $this->hasOne(\app\models\Contract::class, ['contract_id' => 'contract_id']);
  }

  public function getDeals()
  {
    return $this->hasOne(Deals::class, ['deals_id' => 'deals_id']);
  }
  public static function data_notifcontract_all_detail($filter = [])
{
    $query = self::find()
        ->with(['contract.invoice.deals.customer']) // relasi berantai
        ->where(['notification_contract.deleted_at' => null]);

    if (!empty($filter)) {
        $query->andFilterWhere($filter);
    }

    $notifContracts = $query->all();
    $list = [];

    foreach ($notifContracts as $notif) {
        $list[$notif->id_notification_contract] = [
            'id_notification_contract' => $notif->id_notification_contract,
            'nama_customer' => $notif->contract->invoice->deals->customer->customer_name ?? '-', // relasi berantai
            'tanggal' => Yii::$app->formatter->asDate($notif->date_notificatian_contract),
            'status' => $notif->status_contract_notification ?? '-',
            'deskripsi' => $notif->description ?? '-',
        ];
    }

    return $list;
}

}
