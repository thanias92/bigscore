<?php

namespace app\models;

use Yii;
use app\traits\AuditTrailTrait;

/**
 * This is the model class for table "pengeluaran".
 *
 * @property int $id_pengeluaran
 * @property string $tanggal
 * @property string|null $jumlah
 * @property string|null $jenis_pembayaran
 * @property int|null $id_vendor
 * @property int|null $no_pengeluaran
 * @property string|null $keterangan
 * @property int $created_by
 * @property int $updated_by
 * @property int $deleted_by
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 * @property Accountkeluar  $accountkeluar
 */
class Pengeluaran extends \yii\db\ActiveRecord
{
  use AuditTrailTrait;
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'pengeluaran';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['jumlah', 'jenis_pembayaran', 'keterangan', 'deleted_at', 'id_vendor'], 'default', 'value' => null],
      [['tanggal', 'jumlah', 'accountkeluar_id', 'jenis_pembayaran', 'keterangan'], 'required'],
      [['id_vendor', 'accountkeluar_id'], 'integer'],
      [['tanggal', 'created_at', 'updated_at', 'deleted_at', 'status_pembayaran', 'bukti_pembayaran'], 'safe'],
      [['created_by', 'updated_by', 'deleted_by', 'no_pengeluaran'], 'default', 'value' => 0],
      [['created_by', 'updated_by', 'deleted_by'], 'integer'],
      [['jumlah', 'jenis_pembayaran', 'id_vendor', 'keterangan'], 'string', 'max' => 255],
    ];
  }

  /**
   * {@inheritdoc}
   */


  public function attributeLabels()
  {
    return [
      'id_pengeluaran' => Yii::t('app', 'Id Pengeluaran'),
      'tanggal' => Yii::t('app', 'Date Created'),
      'jumlah' => Yii::t('app', 'Amount of expenditure '),
      'jenis_pembayaran' => Yii::t('app', 'Payment Type'),
      'accountkeluar_id' => Yii::t('app', 'Expense Account'),
      'id_vendor' => Yii::t('app', 'Vendor'),
      'no_pengeluaran' => Yii::t('app', 'Expenditure No'),
      'status_pembayaran' => Yii::t('app', 'Payment Status'),
      'bukti_pembayaran'  =>  Yii::t('app', 'Proof of payment'),
      'keterangan' => Yii::t('app', 'Description'),
      'created_by' => Yii::t('app', 'Created By'),
      'updated_by' => Yii::t('app', 'Updated By'),
      'deleted_by' => Yii::t('app', 'Deleted By'),
      'created_at' => Yii::t('app', 'Created At'),
      'updated_at' => Yii::t('app', 'Updated At'),
      'deleted_at' => Yii::t('app', 'Deleted At'),
    ];
  }
  public function getVendor()
  {
    return $this->hasOne(Vendor::class, ['id_vendor' => 'id_vendor']);
  }
  public function getAccountkeluar()
  {
    return $this->hasOne(Accountkeluar::class, ['id' => 'accountkeluar_id']);
  }

  public static function data_pengeluaran_all()
  {
    $pengeluarans = Pengeluaran::find()
      ->with(['vendor', 'accountkeluar']) // pastikan relasi tersedia
      ->where(['deleted_at' => null])
      ->all();

    $list_pengeluaran = [];

    foreach ($pengeluarans as $pengeluaran) {
      $list_pengeluaran[$pengeluaran->id_pengeluaran] = [
        'tanggal' => $pengeluaran->tanggal,
        'jumlah' => $pengeluaran->jumlah,
        'jenis_pembayaran' => $pengeluaran->jenis_pembayaran,
        'status_pembayaran' => $pengeluaran->status_pembayaran,
        'vendor' => $pengeluaran->vendor->nama_vendor ?? '-',
        'akun_keluar' => $pengeluaran->accountkeluar->akun ?? '-',
        'keterangan' => $pengeluaran->keterangan,
      ];
    }

    return $list_pengeluaran;
  }
  public function beforeSave($insert)
  {
    if (parent::beforeSave($insert)) {
      if (!empty($this->tanggal)) {
        // Cek apakah format sudah Y-m-d (dari update)
        $date = \DateTime::createFromFormat('Y-m-d', $this->tanggal);
        if (!$date) {
          // Kalau bukan Y-m-d, coba d/m/Y (dari input create)
          $date = \DateTime::createFromFormat('d/m/Y', $this->tanggal);
        }

        if ($date) {
          $this->tanggal = $date->format('Y-m-d');
        } else {
          $this->addError('tanggal', 'Format tanggal tidak valid. Gunakan format dd/mm/yyyy.');
          return false;
        }
      }
      return true;
    }
    return false;
  }
}
