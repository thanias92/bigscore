<?php

namespace app\models;

use Yii;
use app\traits\AuditTrailTrait;

/**
 * This is the model class for table "pemasukan_cicilan".
 *
 * @property int $id
 * @property int $pemasukan_id
 * @property int $ke
 * @property string $jatuh_tempo
 * @property float $nominal
 * @property string $status
 * @property string|null $tanggal_bayar
 * @property string|null $bukti_path
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 *
 * @property Pemasukan $pemasukan
 */
class PemasukanCicilan extends \yii\db\ActiveRecord
{
  use AuditTrailTrait;
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'pemasukan_cicilan';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['tanggal_bayar', 'bukti_path', 'deleted_at', 'created_by', 'updated_by', 'deleted_by'], 'default', 'value' => null],
      [['status'], 'default', 'value' => 'Menunggu'],
      [['pemasukan_id', 'ke', 'jatuh_tempo'], 'required'],
      [['pemasukan_id', 'ke', 'created_by', 'updated_by', 'deleted_by'], 'default', 'value' => null],
      [['pemasukan_id', 'ke', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
      [['jatuh_tempo', 'tanggal_bayar', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
      [['nominal'], 'number'],
      [['status'], 'string', 'max' => 20],
      [['bukti_path'], 'string', 'max' => 255],
      [['pemasukan_id', 'ke'], 'unique', 'targetAttribute' => ['pemasukan_id', 'ke']],
      [['pemasukan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pemasukan::class, 'targetAttribute' => ['pemasukan_id' => 'pemasukan_id']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => Yii::t('app', 'ID'),
      'pemasukan_id' => Yii::t('app', 'Pemasukan ID'),
      'ke' => Yii::t('app', 'Ke'),
      'jatuh_tempo' => Yii::t('app', 'Jatuh Tempo'),
      'nominal' => Yii::t('app', 'Nominal'),
      'status' => Yii::t('app', 'Status'),
      'tanggal_bayar' => Yii::t('app', 'Tanggal Bayar'),
      'bukti_path' => Yii::t('app', 'Bukti Path'),
      'created_at' => Yii::t('app', 'Created At'),
      'updated_at' => Yii::t('app', 'Updated At'),
      'deleted_at' => Yii::t('app', 'Deleted At'),
      'created_by' => Yii::t('app', 'Created By'),
      'updated_by' => Yii::t('app', 'Updated By'),
      'deleted_by' => Yii::t('app', 'Deleted By'),
    ];
  }

  /**
   * Gets query for [[Pemasukan]].
   *
   * @return \yii\db\ActiveQuery
   */
  public function getPemasukan()
  {
    return $this->hasOne(Pemasukan::class, ['pemasukan_id' => 'pemasukan_id']);
  }
  public function getPenerimaanPembayaran()
  {
    return $this->hasOne(PenerimaanPembayaran::class, ['pemasukan_cicilan_id' => 'id']);
  }

}
