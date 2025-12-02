<?php

namespace app\models;

use Yii;
use app\traits\AuditTrailTrait;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "vendor".
 *
 * @property int $id_vendor
 * @property string $nama_vendor
 * @property string|null $alamat_vendor
 * @property string|null $email_vendor
 * @property int|null $telp_vendor
 * @property string|null $nama_PIC
 * @property string|null $email_PIC
 * @property int|null $telp_PIC
 * @property int $created_by
 * @property int $updated_by
 * @property int $deleted_by
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 */
class Vendor extends \yii\db\ActiveRecord
{
  use AuditTrailTrait;
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'vendor';
  }

  public function behaviors()
  {
    return [
      'timestamp' => [
        'class' => TimestampBehavior::class,
        'createdAtAttribute' => 'created_at',
        'updatedAtAttribute' => 'updated_at',
        'value' => function () {
          return date('Y-m-d H:i:s');
        },
      ],
      'blameable' => [
        'class' => BlameableBehavior::class,
        'createdByAttribute' => 'created_by',
        'updatedByAttribute' => 'updated_by',
      ],
    ];
  }
  public function actionDelete($id_vendor)
  {
    $model = $this->findModel($id_vendor);
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

    $model->deleted_by = Yii::$app->user->id;
    $model->deleted_at = date('Y-m-d H:i:s');

    if ($model->save(false)) {
      return [
        'status' => 'success',
        'message' => 'Berhasil Menghapus Data'
      ];
    } else {
      return [
        'status' => 'failed',
        'message' => 'Gagal Menghapus Data'
      ];
    }
  }


  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['alamat_vendor', 'email_vendor', 'telp_vendor', 'nama_PIC', 'email_PIC', 'telp_PIC', 'deleted_at'], 'default', 'value' => null],

      // Kolom ID pembuat/pengubah tetap integer
      [['created_by', 'updated_by', 'deleted_by'], 'integer'],

      [['created_at', 'updated_at', 'deleted_at'], 'safe'],
      [['deleted_by'], 'default', 'value' => 0],

      // Validasi required untuk semua input penting
      [['nama_vendor', 'alamat_vendor', 'email_vendor', 'telp_vendor', 'nama_PIC', 'email_PIC', 'telp_PIC'], 'required'],

      // Validasi panjang string (nama, alamat, email)
      [['nama_vendor', 'alamat_vendor', 'email_vendor', 'nama_PIC', 'email_PIC'], 'string', 'max' => 255],

      // Validasi nomor telepon (jadi string sekarang)
      [['telp_vendor', 'telp_PIC'], 'string', 'max' => 20],

      // Validasi email
      [['email_vendor', 'email_PIC'], 'email'],

      // Validasi format nomor telepon: angka, +, dan - diperbolehkan
      [['telp_vendor', 'telp_PIC'], 'match', 'pattern' => '/^[0-9\+\-]+$/', 'message' => 'Hanya boleh angka dan tanda + atau -'],
    ];
  }


  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id_vendor' => Yii::t('app', 'Id Vendor'),
      'nama_vendor' => Yii::t('app', 'Nama Vendor'),
      'alamat_vendor' => Yii::t('app', 'Alamat Vendor'),
      'email_vendor' => Yii::t('app', 'Email Vendor'),
      'telp_vendor' => Yii::t('app', 'Telp Vendor'),
      'nama_PIC' => Yii::t('app', 'Nama Pic'),
      'email_PIC' => Yii::t('app', 'Email Pic'),
      'telp_PIC' => Yii::t('app', 'Telp Pic'),
      'created_by' => Yii::t('app', 'Created By'),
      'updated_by' => Yii::t('app', 'Updated By'),
      'deleted_by' => Yii::t('app', 'Deleted By'),
      'created_at' => Yii::t('app', 'Created At'),
      'updated_at' => Yii::t('app', 'Updated At'),
      'deleted_at' => Yii::t('app', 'Deleted At'),
    ];
  }

  /**
   * {@inheritdoc}
   * @return VendorQuery the active query used by this AR class.
   */
  public static function find()
  {
    return new VendorQuery(get_called_class());
  }

  public static function getDataList($query = null)
  {
    $data = static::find()
      ->select(['id_vendor', 'nama_vendor', 'email_vendor', 'telp_vendor', 'alamat_vendor'])
      ->where(['deleted_at' => null])
      ->andFilterWhere([
        'or',
        ['ilike', 'nama_vendor', $query],
        ['ilike', 'email_vendor', $query],
        ['ilike', 'telp_vendor', $query],
        ['ilike', 'alamat_vendor', $query],
      ])
      ->asArray()
      ->all();

    return array_map(function ($item) {
      return [
        'id' => $item['id_vendor'],
        'text' => "{$item['nama_vendor']} - {$item['telp_vendor']} - {$item['email_vendor']}",
      ];
    }, $data);
  }
}
