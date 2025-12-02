<?php

namespace app\models;

use Yii;
use app\traits\AuditTrailTrait;

/**
 * This is the model class for table "pengaturanakun".
 *
 * @property int $pengaturanakun_id
 * @property string|null $logo
 * @property string|null $pemasukan_id
 * @property string|null $ttd
 * @property string|null $deleted_at
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_by
 * @property string|null $created_by
 * @property string|null $updated_by
 */
class Pengaturanakun extends \yii\db\ActiveRecord
{
  use AuditTrailTrait;
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'pengaturanakun';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['logo', 'ttd', 'pemasukan_id'], 'default', 'value' => null],
      [['created_at', 'updated_at', 'deleted_at'], 'safe'],
      [['logo', 'ttd'], 'string', 'max' => 255],
      // Validasi untuk upload file logo
      [['logoFile'], 'file', 'skipOnEmpty' => true, 'extensions' => ['png', 'jpg', 'jpeg'], 'maxSize' => 1024 * 1024], // max 1MB
      // Validasi untuk signature (base64 string dari canvas)
      [['nama_perusahaan', 'email', 'signatureData'], 'string'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'pengaturanakun_id' => Yii::t('app', 'ID'),
      'logo' => Yii::t('app', 'Logo'),
      'ttd' => Yii::t('app', 'Ttd'),
      'nama_perusahaan' => Yii::t('app', 'Nama Perusahaan'),
      'email' => Yii::t('app', 'Email'),
      'pemasukan_id' => Yii::t('app', 'Data Perusahaan'),
      'created_at' => Yii::t('app', 'Created At'),
      'updated_at' => Yii::t('app', 'Updated At'),
      'deleted_at' => Yii::t('app', 'Deleted At'),
      'created_by' => Yii::t('app', 'Created By'),
      'updated_by' => Yii::t('app', 'Updated By'),
      'deleted_by' => Yii::t('app', 'Deleted By'),
    ];
  }

  public $logoFile;
  public $signatureData;
  public $nama_perusahaan;
  public $email;

  public function getPemasukan()
  {
    return $this->hasOne(Pemasukan::class, ['pemasukan_id' => 'pemasukan_id']);
  }


  public function getNama_perusahaan()
  {
    return 'PT. Bigs Integrasi Teknologi';
  }

  public function getEmail()
  {
    return $this->pemasukan ? $this->pemasukan->pengirim_email : null;
  }

  public function getPhone()
  {
    return $this->pemasukan ? $this->pemasukan->phone : null;
  }
  public function getLogoUrl()
  {
    return $this->logo   // contoh: “/uploads/logo_123.png”
      ? \Yii::getAlias('@web') . $this->logo
      : null;
  }

  public function getTtdUrl()
  {
    return $this->ttd
      ? \Yii::getAlias('@web') . $this->ttd
      : null;
  }
}
