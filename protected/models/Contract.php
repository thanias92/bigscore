<?php

namespace app\models;

use Yii;
use app\models\ContractHistory;
use yii\db\ActiveRecord;
use app\traits\AuditTrailTrait;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "contract".
 *
 * @property int $contract_id
 * @property string $contract_code
 * @property int $invoice_id
 * @property string|null $start_date
 * @property string|null $end_date
 * @property string $evidence_contract
 * @property string $status_contract
 * @property string|null $description
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 *
 * @property Pemasukan $invoice
 */
class Contract extends \yii\db\ActiveRecord
{
  use AuditTrailTrait;

  /**
   * Atribut virtual untuk upload file (harus public)
   */
  public $uploadFile;

  public function behaviors()
  {
    return [
      [
        'class' => TimestampBehavior::class,
        'value' => function () {
          return date('Y-m-d');
        },
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'contract';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['start_date', 'end_date', 'description', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at'], 'default', 'value' => null],
      [['contract_code', 'invoice_id', 'status_contract'], 'required'],
      [['invoice_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
      [['start_date', 'end_date', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
      [['description'], 'string'],
      [['contract_code', 'status_contract'], 'string', 'max' => 255],

      // ✅ Validasi file upload PDF, PNG, JPG
      [['uploadFile'], 'required', 'on' => 'create', 'message' => 'Please upload a file.'],
      [['uploadFile'], 'file', 'skipOnEmpty' => true, 'extensions' => ['pdf'], 'message' => 'Only PDF files are allowed.'],

      [['invoice_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pemasukan::class, 'targetAttribute' => ['invoice_id' => 'pemasukan_id']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'contract_id' => 'Contract ID',
      'contract_code' => 'Contract Code',
      'invoice_id' => 'Invoice Code',
      'start_date' => 'Start Date',
      'end_date' => 'End Date',
      'evidence_contract' => 'Contract File',
      'uploadFile' => 'Upload Contract File',
      'status_contract' => 'Status Contract',
      'description' => 'Description',
      'created_by' => 'Created By',
      'updated_by' => 'Updated By',
      'deleted_by' => 'Deleted By',
      'created_at' => 'Created At',
      'updated_at' => 'Updated At',
      'deleted_at' => 'Deleted At',
    ];
  }

  public function afterSave($insert, $changedAttributes)
  {
    parent::afterSave($insert, $changedAttributes);

    // 1. Logika untuk NotificationContract 
    if ($insert) {
      $notification = new \app\models\NotificationContract();
      $notification->contract_id = $this->contract_id;
      $notification->status_contract_notification = '';
      $notification->date_notificatian_contract = date('Y-m-d');
      $notification->created_at = date('Y-m-d');
      $notification->save(false);
    }

    // 2. Logika BARU untuk ContractHistory
    if ($insert) {
      // Log untuk data baru
      $history = new ContractHistory();
      $history->contract_id = $this->contract_id;
      $history->activity_type = 'create';
      $history->description = "New contract created '{$this->contract_code}' for Invoice '{$this->invoice->no_faktur}'.";
      $history->created_by = Yii::$app->user->id ?? null;
      $history->created_at = $this->created_at;
      $history->save(false);
    } else {
      // Log untuk data yang diupdate
      foreach ($changedAttributes as $attribute => $oldValue) {
        $newValue = $this->getAttribute($attribute);

        // Lakukan perbandingan standar terlebih dahulu
        if ($oldValue == $newValue || in_array($attribute, ['updated_at', 'updated_by'])) {
          continue;
        }

        // TAMBAHAN: Lakukan pengecekan khusus untuk tanggal jika perbandingan standar gagal
        if (in_array($attribute, ['start_date', 'end_date'])) {
          if (Yii::$app->formatter->asDate($oldValue, 'yyyy-MM-dd') == Yii::$app->formatter->asDate($newValue, 'yyyy-MM-dd')) {
            continue;
          }
        }

        $fieldLabel = $this->getAttributeLabel($attribute);

        // Format nilai agar lebih mudah dibaca (opsional, bisa dikembangkan)
        $oldDisplayValue = $oldValue;
        $newDisplayValue = $newValue;
        if (in_array($attribute, ['start_date', 'end_date'])) {
          $oldDisplayValue = Yii::$app->formatter->asDate($oldValue, 'php:d M Y');
          $newDisplayValue = Yii::$app->formatter->asDate($newValue, 'php:d M Y');
        }

        $history = new ContractHistory();
        $history->contract_id = $this->contract_id;
        $history->activity_type = 'update';
        $history->field_changed = $attribute;
        $history->old_value = is_string($oldValue) ? $oldValue : json_encode($oldValue);
        $history->new_value = is_string($newValue) ? $newValue : json_encode($newValue);
        $history->description = "{$fieldLabel} changed from '{$oldDisplayValue}' to '{$newDisplayValue}'";
        $history->created_by = Yii::$app->user->id ?? null;
        $history->created_at = $this->updated_at;
        $history->save(false);
      }
    }
  }

  public static function getContractStatusList()
  {
    return [
      'Active' => 'Active',
      'Inactive' => 'Inactive',
    ];
  }

  /**
   * Gets query for [[Invoice]].
   *
   * @return \yii\db\ActiveQuery
   */
  public function getInvoice()
  {
    return $this->hasOne(Pemasukan::class, ['pemasukan_id' => 'invoice_id']);
  }

  public function getDeals()
  {
    return $this->hasOne(Deals::class, ['deals_id' => 'deals_id'])
      ->via('invoice');
  }

  public function getCustomer()
  {
    return $this->hasOne(Customer::class, ['customer_id' => 'customer_id'])
      ->via('deals');
  }

  public function getProduct()
  {
    return $this->hasOne(Product::class, ['id_produk' => 'product_id'])
      ->via('deals');
  }

  public function getContractHistories()
  {
    return $this->hasMany(ContractHistory::class, ['contract_id' => 'contract_id'])
      ->with('createdBy')
      ->orderBy(['created_at' => SORT_DESC]);
  }
}
