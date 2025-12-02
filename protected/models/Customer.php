<?php

namespace app\models;

use Yii;
use app\models\CustomerHistory;
use app\models\CustomerVisit;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class Customer extends ActiveRecord
{
  public function behaviors()
  {
    return [
      [
        'class' => TimestampBehavior::class,
        'value' => function () {
          return date('Y-m-d'); // ini penting!
        },
      ],
    ];
  }

  public static function tableName()
  {
    return 'customer';
  }

  public function rules()
  {
    return [
      [['customer_code', 'customer_name', 'customer_email', 'pic_name'], 'required'],
      [['establishment_date', 'created_at', 'updated_at'], 'safe'],
      [['created_by', 'updated_by'], 'integer'],
      [['customer_name', 'customer_email', 'customer_phone', 'customer_address', 'customer_website', 'customer_source', 'pic_name', 'pic_email', 'pic_phone', 'pic_workroles'], 'string', 'max' => 255],
      [['customer_email', 'pic_email'], 'email'],
    ];
  }

  public function attributeLabels()
  {
    return [
      'customer_id' => 'Customer ID',
      'customer_code' => 'Customer Code',
      'customer_name' => 'Name',
      'customer_address' => 'Address',
      'customer_email' => 'Email ',
      'customer_phone' => 'Phone',
      'customer_website' => 'Website',
      'establishment_date' => 'Incorporation Date',
      'customer_source' => 'Source',
      'pic_name' => 'Name',
      'pic_workroles' => 'Position',
      'pic_email' => 'Email',
      'pic_phone' => 'Phone',
      'created_by' => 'Sales',
    ];
  }

  public function getCreatedBy()
  {
    return $this->hasOne(User::class, ['id' => 'created_by']);
  }

  public function getCustomerHistories()
  {
    return $this->hasMany(CustomerHistory::class, ['customer_id' => 'customer_id'])
      ->with('createdBy')
      ->orderBy(['created_at' => SORT_DESC]);
  }

  public static function getSourceList()
  {
    return [
      'satusehat' => 'SATUSEHAT Data',
      'bps' => 'Badan Pusat Statistik (BPS)',
      'eklinik' => 'eKlinik',
      'dinkes_riau' => 'Dinas Kesehatan Riau',
    ];
  }

  // --- TAMBAHAN: Relasi baru ke tabel CustomerVisit ---
  public function getCustomerVisits()
  {
    return $this->hasMany(CustomerVisit::class, ['customer_id' => 'customer_id'])
      ->with('createdBy')
      ->orderBy(['visit_date' => SORT_DESC]);
  }

  // --- TAMBAHAN: Fungsi untuk menggabungkan semua riwayat ---
  public function getUnifiedHistory()
  {
    $histories = $this->customerHistories;
    $visits = $this->customerVisits;

    $unified = [];

    // Proses data dari customer_history
    foreach ($histories as $history) {
      $unified[] = [
        'type' => 'history',
        'timestamp' => strtotime($history->created_at),
        'date' => $history->created_at,
        'user' => $history->createdBy->username ?? 'System',
        'description' => $history->description,
        'activity_type' => $history->activity_type,
      ];
    }

    // Proses data dari customer_visit
    foreach ($visits as $visit) {
      $unified[] = [
        'type' => 'visit',
        // Gunakan created_at dari visit untuk pengurutan yang akurat
        'timestamp' => $visit->created_at,
        'date' => $visit->visit_date,
        'user' => $visit->createdBy->username ?? 'System',
        'description' => "Visit logged with notes: \n" . $visit->notes,
        'activity_type' => 'visit', // Class CSS untuk timeline
      ];
    }

    // Urutkan semua riwayat berdasarkan waktu (timestamp) secara descending (terbaru di atas)
    ArrayHelper::multisort($unified, ['timestamp'], [SORT_DESC]);

    return $unified;
  }

  public function beforeSave($insert)
  {
    if (!parent::beforeSave($insert)) {
      return false;
    }

    if ($this->isNewRecord) {
      $this->created_by = Yii::$app->user->id;
    }
    $this->updated_by = Yii::$app->user->id;

    return true;
  }

  // Method afterSave untuk mencatat history
  public function afterSave($insert, $changedAttributes)
  {
    parent::afterSave($insert, $changedAttributes);

    if ($insert) {
      // Logika untuk customer BARU
      $history = new CustomerHistory();
      $history->customer_id = $this->customer_id;
      $history->activity_type = 'create';

      // PERBAIKAN FATAL ERROR: Langsung gunakan $this->customer_name
      $customerName = $this->customer_name ?? 'N/A';

      $history->description = "New customer created '{$this->customer_code} - {$customerName}'";
      $history->created_by = Yii::$app->user->id ?? null;
      $history->created_at = date('Y-m-d'); // Gunakan format datetime lengkap
      $history->save(false);
    } else {
      // Logika untuk UPDATE customer (meniru dari Deals.php)
      foreach ($changedAttributes as $attribute => $oldValue) {
        $newValue = $this->getAttribute($attribute);

        // Lewati jika nilainya sama atau jika hanya kolom 'updated_at'/'updated_by'
        if ($oldValue == $newValue || in_array($attribute, ['updated_at', 'updated_by'])) {
          continue;
        }

        $fieldLabel = $this->getAttributeLabel($attribute);

        $history = new CustomerHistory();
        $history->customer_id = $this->customer_id;
        $history->activity_type = 'update';
        $history->field_changed = $attribute;
        $history->old_value = is_string($oldValue) ? $oldValue : json_encode($oldValue);
        $history->new_value = is_string($newValue) ? $newValue : json_encode($newValue);
        $history->description = "{$fieldLabel} changed from '{$oldValue}' to '{$newValue}'";
        $history->created_by = Yii::$app->user->id ?? null;
        $history->created_at = date('Y-m-d');
        $history->save(false);
      }
    }
  }
}
