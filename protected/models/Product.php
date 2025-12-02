<?php

namespace app\models;

use Yii;
use app\traits\AuditTrailTrait;

/**
 * This is the model class for table "product".
 *
 * @property int $id_produk
 * @property string $no_produk
 * @property string $code_produk
 * @property string|null $keterangan
 * @property int|null $unit
 * @property string $product_name
 * @property string|null $harga
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property string|null $created_at
 * @property string|null $updated_at  
 * @property string|null $deleted_at
 */
class Product extends \yii\db\ActiveRecord
{
  use AuditTrailTrait;
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'product';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['keterangan', 'unit', 'harga', 'created_by', 'updated_by', 'deleted_by', 'deleted_at'], 'default', 'value' => null],
      [['code_produk', 'product_name'], 'required'],
      [['unit', 'created_by', 'updated_by', 'deleted_by'], 'default', 'value' => null],
      [['unit', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
      [['created_at', 'updated_at', 'deleted_at'], 'safe'],
      [['code_produk', 'keterangan', 'product_name', 'harga'], 'string', 'max' => 255],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id_produk' => Yii::t('app', 'Id Produk'),
      'no_produk' => Yii::t('app', 'No Produk'),
      'code_produk' => Yii::t('app', 'Code Produk'),
      'keterangan' => Yii::t('app', 'Keterangan'),
      'unit' => Yii::t('app', 'Unit'),
      'product_name' => Yii::t('app', 'Product Name'),
      'harga' => Yii::t('app', 'Harga'),
      'created_by' => Yii::t('app', 'Created By'),
      'updated_by' => Yii::t('app', 'Updated By'),
      'deleted_by' => Yii::t('app', 'Deleted By'),
      'created_at' => Yii::t('app', 'Created At'),
      'updated_at' => Yii::t('app', 'Updated At'),
      'deleted_at' => Yii::t('app', 'Deleted At'),
    ];
  }

  public static function data_product_all()
  {
    $products = Product::find()->all();
    $list_nama_product = [];

    foreach ($products as $product) {
      $list_nama_product[$product->id_produk] = [
        'product_name' => $product->product_name,
        'code_produk' => $product->code_produk,
        'no_produk' => $product->no_produk,
        'harga' => $product->harga,
      ];
    }

    return $list_nama_product;
  }
}
