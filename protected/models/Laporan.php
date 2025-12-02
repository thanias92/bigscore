<?php

namespace app\models;

use Yii;
use app\traits\AuditTrailTrait;

/**
 * This is the model class for table "laporan".
 *
 * @property int $laporan_id
 * @property int $pemasukan_id
 * @property int $pengeluaran_id
 * @property string $tanggal
 * @property string $tipe_laporan
 * @property int|null $jumlah_pemasukan
 * @property int|null $jumlah_pengeluaran
 * @property int|null $saldo_akhir
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 *
 * @property Pemasukan $pemasukan
 * @property Pengeluaran $pengeluaran
 */
class Laporan extends \yii\db\ActiveRecord
{
  use AuditTrailTrait;
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'laporan';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['jumlah_pemasukan', 'jumlah_pengeluaran', 'saldo_akhir', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at'], 'default', 'value' => null],
      [['pemasukan_id', 'pengeluaran_id', 'tanggal', 'tipe_laporan'], 'required'],
      [['pemasukan_id', 'pengeluaran_id', 'jumlah_pemasukan', 'jumlah_pengeluaran', 'saldo_akhir', 'created_by', 'updated_by', 'deleted_by'], 'default', 'value' => null],
      [['pemasukan_id', 'pengeluaran_id', 'jumlah_pemasukan', 'jumlah_pengeluaran', 'saldo_akhir', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
      [['tanggal', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
      [['tipe_laporan'], 'string', 'max' => 50],
      [['pemasukan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pemasukan::class, 'targetAttribute' => ['pemasukan_id' => 'pemasukan_id']],
      [['pengeluaran_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pengeluaran::class, 'targetAttribute' => ['pengeluaran_id' => 'id_pengeluaran']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'laporan_id' => Yii::t('app', 'Laporan ID'),
      'pemasukan_id' => Yii::t('app', 'Pemasukan ID'),
      'pengeluaran_id' => Yii::t('app', 'Pengeluaran ID'),
      'tanggal' => Yii::t('app', 'Tanggal'),
      'tipe_laporan' => Yii::t('app', 'Tipe Laporan'),
      'jumlah_pemasukan' => Yii::t('app', 'Jumlah Pemasukan'),
      'jumlah_pengeluaran' => Yii::t('app', 'Jumlah Pengeluaran'),
      'saldo_akhir' => Yii::t('app', 'Saldo Akhir'),
      'created_by' => Yii::t('app', 'Created By'),
      'updated_by' => Yii::t('app', 'Updated By'),
      'deleted_by' => Yii::t('app', 'Deleted By'),
      'created_at' => Yii::t('app', 'Created At'),
      'updated_at' => Yii::t('app', 'Updated At'),
      'deleted_at' => Yii::t('app', 'Deleted At'),
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

  /**
   * Gets query for [[Pengeluaran]].
   *
   * @return \yii\db\ActiveQuery
   */
  public function getPengeluaran()
  {
    return $this->hasOne(Pengeluaran::class, ['id_pengeluaran' => 'pengeluaran_id']);
  }
}
