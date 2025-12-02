<?php

namespace app\models;

use Yii;
use app\traits\AuditTrailTrait;
use app\models\User;
use app\models\Customer;
use app\models\Product;
use app\models\DealsHistory;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "deals".
 *
 * @property int $deals_id
 * @property int $customer_id
 * @property int $product_id
 * @property string $deals_code
 * @property string $label_deals
 * @property int $unit_product
 * @property int $price_product
 * @property string $total
 * @property string|null $purchase_type
 * @property string|null $purchase_date
 * @property string $description
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 *
 * @property Customer $customer
 * @property Product $product
 * @property DealsHistory[] $dealsHistories      
 */
class Deals extends \yii\db\ActiveRecord
{
  use AuditTrailTrait;
  /**
   * {@inheritdoc}
   */
  public $linked_quotation_id;

  public static function tableName()
  {
    return 'deals';
  }

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

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      // Aturan Wajib Dasar
      [['customer_id', 'deals_code', 'label_deals'], 'required'],

      // Aturan Wajib Kondisional untuk 'product_id'
      // Wajib dari form HANYA jika labelnya 'New' atau label yang bisa diedit.
      [['product_id'], 'required', 'when' => function ($model) {
        return in_array($model->label_deals, ['New', 'Negotiation', 'Deal Won']);
      }, 'message' => 'Product harus dipilih untuk label ini.'],

      // Aturan Wajib Kondisional untuk 'linked_quotation_id'
      // Wajib dari form HANYA jika labelnya 'Proposal Sent'.
      [['linked_quotation_id'], 'required', 'when' => function ($model) {
        return $model->label_deals === 'Proposal Sent';
      }, 'message' => 'Quotation harus dipilih untuk label Proposal Sent.'],

      // Aturan lain (tidak berubah)
      [['price_product', 'unit_product'], 'required', 'when' => function ($model) {
        return in_array($model->label_deals, ['Negotiation', 'Deal Won']);
      }],
      [['customer_id', 'product_id', 'unit_product', 'linked_quotation_id',  'created_by', 'updated_by'], 'integer'],
      [['description'], 'string'],
      [['purchase_date', 'created_at', 'updated_at', 'total', 'price_product'], 'safe'],
      [['label_deals', 'purchase_type'], 'string', 'max' => 255],
      [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::class, 'targetAttribute' => ['customer_id' => 'customer_id']],
      [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['product_id' => 'id_produk']],
      [['purchase_type'], 'in', 'range' => array_keys(self::getPurchaseTypeList())],
      [['label_deals'], 'in', 'range' => array_keys(self::getDealsLabelList())],
      [['purchase_type'], 'required', 'when' => function ($model) {
        return in_array($model->label_deals, ['Negotiation', 'Deal Won', 'Deal Lost']);
      }],
      [['purchase_date'], 'required', 'when' => function ($model) {
        return $model->label_deals === 'Deal Won';
      }],
    ];
  }

  public function attributeLabels()
  {
    return [
      'deals_id' => 'Deals ID',
      'customer_id' => 'Customer',
      'product_id' => 'Product',
      'deals_code' => 'Deals Code',
      'label_deals' => 'Label Deals',
      'unit_product' => 'Unit Product',
      'price_product' => 'Price Product',
      'total' => 'Total',
      'purchase_type' => 'Purchase Type',
      'purchase_date' => 'Purchase Date',
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
   * Gets query for [[Customer]].
   *
   * @return \yii\db\ActiveQuery
   */
  public function getCustomer()
  {
    return $this->hasOne(Customer::class, ['customer_id' => 'customer_id']);
  }

  /**
   * Gets query for [[Product]].
   *
   * @return \yii\db\ActiveQuery
   */
  public function getProduct()
  {
    return $this->hasOne(Product::class, ['id_produk' => 'product_id']);
  }

  public function getCreatedBy()
  {
    // Menghubungkan kolom 'created_by' di tabel 'deals' 
    // dengan kolom 'id' di tabel 'user'.
    return $this->hasOne(User::class, ['id' => 'created_by']);
  }

  /**
   * Gets query for [[DealsHistories]].
   *
   * @return \yii\db\ActiveQuery
   */
  public function getDealsHistories()
  {
    return $this->hasMany(DealsHistory::class, ['deals_id' => 'deals_id'])
      ->with('createdBy')
      ->orderBy(['created_at' => SORT_DESC]);
  }

  /**
   * Get a list of possible deals labels.
   * @return array
   */
  public static function getDealsLabelList()
  {
    return [
      'New' => 'New',
      'Proposal Sent' => 'Proposal Sent',
      'Negotiation' => 'Negotiation',
      'Deal Won' => 'Deal Won',
      'Deal Lost' => 'Deal Lost',
    ];
  }

  /**
   * Get a list of possible purchase types.
   * @return array
   */
  public static function getPurchaseTypeList()
  {
    return [
      'Subscription (Monthly)' => 'Subscription (Monthly)',
      'Outright Purchase - Full Payment' => 'Outright Purchase - Full Payment',
      'Outright Purchase - Installments' => 'Outright Purchase - Installments',
    ];
  }

  public function beforeSave($insert)
  {
    if (!parent::beforeSave($insert)) {
      return false;
    }

    // Mengisi ID user yang membuat/mengubah data deal
    if ($this->isNewRecord) {
      $this->created_by = Yii::$app->user->id;
    }
    $this->updated_by = Yii::$app->user->id;

    $newLabel = $this->label_deals;

    // Logika State Machine berdasarkan label yang akan disimpan
    switch ($newLabel) {
      case 'New':
        // Sumber data dari Master Produk.
        $product = Product::findOne($this->product_id);
        if ($product) {
          // Pastikan nama field harga dan unit sesuai dengan tabel Produk Anda.
          $this->price_product = $product->harga ?? 0;
          $this->unit_product = $product->unit ?? 1;
        }
        break;

      case 'Proposal Sent':
        // Sumber data dari Quotation.
        // Ini adalah perbaikan utama: kita mengisi data yang hilang dari form.
        if (!empty($this->linked_quotation_id)) {
          $quotation = Quotation::findOne($this->linked_quotation_id);
          if ($quotation) {
            // Pastikan nama atribut sesuai dengan model Quotation Anda.
            $this->product_id = $quotation->product_id;
            $this->unit_product = $quotation->unit_product ?? 1;
            $this->price_product = $quotation->price_product ?? 0;
          }
        }
        break;

      case 'Deal Lost':
        // Kunci data, ambil dari nilai sebelum diubah.
        if (!$this->isNewRecord) {
          $this->product_id = $this->getOldAttribute('product_id');
          $this->unit_product = $this->getOldAttribute('unit_product');
          $this->price_product = $this->getOldAttribute('price_product');
        }
        break;

        // Untuk 'Negotiation' dan 'Deal Won', biarkan nilai dari form digunakan.
        // Tidak perlu ada case khusus di sini.
    }

    // --- PERBAIKAN FATAL ERROR ---
    // Pastikan price dan unit adalah angka sebelum dikalikan.
    $price = is_numeric($this->price_product) ? $this->price_product : 0;
    $unit = is_numeric($this->unit_product) ? $this->unit_product : 0;
    $this->total = $price * $unit;

    return true;
  }

  /**
   * {@inheritdoc}
   */
  public function afterSave($insert, $changedAttributes)
  {
    // 1. Panggil parent::afterSave() di baris pertama, ini penting.
    parent::afterSave($insert, $changedAttributes);

    // 2. LOGIKA LAMA ANDA (untuk junction table) - BIARKAN SEPERTI ADANYA
    if (!empty($this->linked_quotation_id)) {
      DealQuotations::updateAll(['is_active' => false], ['deal_id' => $this->deals_id]);

      $dealQuotation = DealQuotations::findOne([
        'deal_id' => $this->deals_id,
        'quotation_id' => $this->linked_quotation_id,
      ]);

      if (!$dealQuotation) {
        $dealQuotation = new DealQuotations();
        $dealQuotation->deal_id = $this->deals_id;
        $dealQuotation->quotation_id = $this->linked_quotation_id;
      }

      $dealQuotation->is_active = true;
      $dealQuotation->save(false);
    }

    // 3. LOGIKA HISTORY BARU (ditempatkan setelah logika lama)
    if ($insert) {
      $history = new DealsHistory();
      $history->deals_id = $this->deals_id;
      $history->activity_type = 'create';
      $customerName = $this->customer->customer_name ?? 'N/A';
      $history->description = "New deal created '{$this->deals_code} - {$customerName}'";
      // Mengisi created_by secara manual, sama seperti di Quotation
      $history->created_by = Yii::$app->user->id ?? null;
      $history->created_at = date('Y-m-d'); // Format lengkap agar bisa diurutkan
      $history->save(false);
    } else {
      foreach ($changedAttributes as $attribute => $oldValue) {
        $newValue = $this->getAttribute($attribute);

        if ($oldValue == $newValue || in_array($attribute, ['updated_at', 'updated_by'])) {
          continue;
        }

        $oldDisplayValue = $oldValue;
        $newDisplayValue = $newValue;
        $fieldLabel = $this->getAttributeLabel($attribute);

        // Logika untuk membuat nilai lebih mudah dibaca (opsional tapi bagus)
        switch ($attribute) {
          case 'customer_id':
            $oldDisplayValue = Customer::findOne($oldValue)->customer_name ?? 'N/A';
            $newDisplayValue = Customer::findOne($newValue)->customer_name ?? 'N/A';
            break;
          case 'product_id':
            $oldDisplayValue = Product::findOne($oldValue)->product_name ?? 'N/A';
            $newDisplayValue = Product::findOne($newValue)->product_name ?? 'N/A';
            break;
          case 'label_deals':
            $list = self::getDealsLabelList();
            $oldDisplayValue = $list[$oldValue] ?? $oldValue;
            $newDisplayValue = $list[$newValue] ?? $newValue;
            break;
        }

        $history = new DealsHistory();
        $history->deals_id = $this->deals_id;
        $history->field_changed = $attribute;
        $history->old_value = is_string($oldValue) ? $oldValue : json_encode($oldValue);
        $history->new_value = is_string($newValue) ? $newValue : json_encode($newValue);
        $history->activity_type = 'update';
        $history->description = "{$fieldLabel} changed from '{$oldDisplayValue}' to '{$newDisplayValue}'";
        $history->created_by = Yii::$app->user->id ?? null;
        $history->created_at = date('Y-m-d');
        $history->save(false);
      }
    }
  }

  public static function data_deals_all()
  {
    $deals = Deals::find()->with('customer')->all(); // pakai eager loading
    $list_deals = [];

    foreach ($deals as $deal) {
      if ($deal->customer) { // pastikan relasinya tidak null
        $list_deals[$deal->deals_id] = $deal->customer->customer_name . ' - ' . $deal->customer->customer_email;
      }
    }

    return $list_deals;
  }

  public function afterFind()
  {
    parent::afterFind();

    // Format tanggal untuk field purchase_date agar bisa ditampilkan di input type="date"
    if ($this->purchase_date) {
      $this->purchase_date = date('Y-m-d', strtotime($this->purchase_date));
    }
  }

  // Gets query for [[DealQuotations]]. Ini adalah relasi ke tabel PENGHUBUNG
  public function getDealQuotations()
  {
    return $this->hasMany(DealQuotations::class, ['deal_id' => 'deals_id']);
  }


  // Gets query for [[Quotations]] via the junction table. Ini adalah relasi ke tabel TUJUAN.
  // Ini akan mengambil SEMUA quotation yang pernah terhubung dengan deal ini.
  public function getQuotations()
  {
    return $this->hasMany(Quotation::class, ['quotation_id' => 'quotation_id'])
      ->via('dealQuotations'); // <-- KEAJAIBANNYA DI SINI
  }

  // Gets query for the ONE active Quotation for this deal.
  // Ini akan mengambil HANYA quotation yang sedang berlaku.
  public function getActiveQuotation()
  {
    return $this->hasOne(Quotation::class, ['quotation_id' => 'quotation_id'])
      ->via('dealQuotations', function ($query) {
        // Menambahkan kondisi pada tabel penghubung
        $query->onCondition(['is_active' => true]);
      });
  }
}
