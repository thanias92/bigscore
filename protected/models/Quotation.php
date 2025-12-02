<?php

namespace app\models;

use Yii;
use app\traits\AuditTrailTrait;
use app\models\QuotationHistory;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "quotation".
 *
 * @property int $quotation_id
 * @property int $customer_id
 * @property int $product_id
 * @property string $quotation_code
 * @property string $quotation_status
 * @property int $unit_product
 * @property int $price_product
 * @property string $total
 * @property string|null $created_date
 * @property string|null $expiration_date
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
 */
class Quotation extends ActiveRecord
{
  use AuditTrailTrait;

  public $linked_deal_id;
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'quotation';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['created_date', 'expiration_date', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at'], 'default', 'value' => null],
      [['customer_id', 'product_id', 'quotation_status'], 'required'],
      [['customer_id', 'product_id', 'unit_product', 'price_product', 'created_by', 'updated_by', 'deleted_by'], 'default', 'value' => null],
      [['customer_id', 'product_id', 'unit_product', 'created_by', 'updated_by', 'deleted_by', 'optional_product_id', 'optional_unit_product', 'linked_deal_id'], 'integer'],
      [['created_date', 'expiration_date', 'created_at', 'updated_at', 'deleted_at' ,'price_product', 'total', 'optional_price_product', 'optional_total'], 'safe'],
      [['description'], 'string'],
      [['quotation_code', 'quotation_status'], 'string', 'max' => 255],
      [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::class, 'targetAttribute' => ['customer_id' => 'customer_id']],
      [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['product_id' => 'id_produk']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'quotation_id' => 'Quotation ID',
      'customer_id' => 'Customer',
      'quotation_code' => 'Quotation Code',
      'quotation_status' => 'Quotation Status',
      // Order Lines
      'product_id' => 'Product',
      'unit_product' => 'Unit Product',
      'price_product' => 'Price Product',
      'total' => 'Total',
      //Optional Products
      'optional_product_id' => 'Optional Product',
      'optional_unit_product' => 'Optional Unit',
      'optional_price_product' => 'Optional Price',
      'optional_total' => 'Optional Total',
      'created_date' => 'Created Date',
      'expiration_date' => 'Expiration Date',
      'description' => 'Description',
      'created_by' => 'Created By',
      'updated_by' => 'Updated By',
      'deleted_by' => 'Deleted By',
      'created_at' => 'Created At',
      'updated_at' => 'Updated At',
      'deleted_at' => 'Deleted At',
      'linked_deal_id' => 'Associated Deal',
    ];
  }

  public static function getQuotationStatusList()
  {
    return [
      'Process' => 'Process',
      'Sent' => 'Sent',
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

  public function getOptionalProduct()
  {
    return $this->hasOne(Product::class, ['id_produk' => 'optional_product_id']);
  }

  public function getQuotationHistories()
  {
    return $this->hasMany(\app\models\QuotationHistory::class, ['quotation_id' => 'quotation_id'])
      ->with('createdBy')
      ->orderBy(['created_at' => SORT_DESC]);
  }

  public function beforeSave($insert)
  {
    if (!parent::beforeSave($insert)) {
      return false;
    }

    // Jika ini adalah record baru, isi created_by
    if ($this->isNewRecord) {
      $this->created_by = Yii::$app->user->id;
    }

    // Selalu isi updated_by saat ada perubahan
    $this->updated_by = Yii::$app->user->id;

    return true;
  }

  public function afterSave($insert, $changedAttributes)
  {
    parent::afterSave($insert, $changedAttributes);

    // JIKA INI ADALAH PEMBUATAN DATA BARU (CREATE)
    if ($insert) {
      $history = new QuotationHistory();
      $history->quotation_id = $this->quotation_id;
      $history->activity_type = 'create';
      $customerName = $this->customer->customer_name ?? 'N/A';
      $history->description = "New Quotation created '{$this->quotation_code} - {$customerName}'";
      $history->created_by = Yii::$app->user->id ?? null;
      $history->created_at = date('Y-m-d');
      $history->save(false);
    }
    // JIKA INI ADALAH PERUBAHAN DATA (UPDATE)
    else {
      foreach ($changedAttributes as $attribute => $oldValue) {
        $newValue = $this->getAttribute($attribute);

        // Kondisi untuk skip:
        // 1. Jika nilai lama dan baru sama persis.
        // 2. Jika field adalah field audit trail itu sendiri.
        if ($oldValue == $newValue || in_array($attribute, ['updated_at', 'updated_by'])) {
          continue;
        }

        // Variabel untuk menyimpan nilai yang akan ditampilkan di log
        $oldDisplayValue = $oldValue;
        $newDisplayValue = $newValue;
        $fieldLabel = $this->getAttributeLabel($attribute); // Ambil label field

        // --- Logika untuk membuat nilai lebih mudah dibaca ---
        switch ($attribute) {
          case 'customer_id':
            // Jika customer berubah, cari nama customer lama dan baru
            $oldCustomer = Customer::findOne($oldValue);
            $newCustomer = Customer::findOne($newValue);
            $oldDisplayValue = $oldCustomer ? $oldCustomer->customer_name : 'N/A';
            $newDisplayValue = $newCustomer ? $newCustomer->customer_name : 'N/A';
            break;

          case 'product_id':
            // Jika produk berubah, cari nama produk lama dan baru
            $oldProduct = Product::findOne($oldValue);
            $newProduct = Product::findOne($newValue);
            $oldDisplayValue = $oldProduct ? $oldProduct->product_name : 'N/A';
            $newDisplayValue = $newProduct ? $newProduct->product_name : 'N/A';
            break;

          case 'created_date':
          case 'expiration_date':
            // Format tanggal agar konsisten dan mudah dibaca
            $oldDisplayValue = Yii::$app->formatter->asDate($oldValue, 'php:d-m-Y');
            $newDisplayValue = Yii::$app->formatter->asDate($newValue, 'php:d-m-Y');
            // Cek lagi setelah diformat, jika sama, jangan catat.
            if ($oldDisplayValue == $newDisplayValue) continue 2; // continue 2 untuk keluar dari switch dan foreach
            break;

          case 'price_product':
          case 'total':
          case 'optional_price_product':
          case 'optional_total':
            // Format manual menggunakan number_format()
            $oldDisplayValue = 'Rp' . number_format((float)$oldValue, 2, ',', '.');
            $newDisplayValue = 'Rp' . number_format((float)$newValue, 2, ',', '.');
            break;
        }

        // Buat deskripsi dalam bahasa Inggris
        $history = new QuotationHistory();
        $history->quotation_id = $this->quotation_id;
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

  public function getCreatedBy()
  {
    return $this->hasOne(User::class, ['id' => 'created_by']);
  }

  public function getDealQuotations()
  {
    return $this->hasMany(DealQuotations::class, ['quotation_id' => 'quotation_id']);
  }

  public function getActiveDeal()
  {
    return $this->hasOne(Deals::class, ['deals_id' => 'deal_id'])
      ->via('dealQuotations', function ($query) {
        // Menambahkan kondisi pada tabel penghubung (junction table)
        $query->onCondition(['is_active' => true]);
      });
  }

  public function getDealSalesperson()
  {
    // Cek apakah relasi activeDeal ada dan tidak null
    if ($this->activeDeal) {
      // Kembalikan objek User dari relasi createdBy milik Deal
      return $this->activeDeal->createdBy;
    }
    return null;
  }
}
