<?php

namespace app\models;

use Yii;
use app\traits\AuditTrailTrait;

/**
 * This is the model class for table "staff".
 *
 * @property int $id_staff
 * @property string $nama_lengkap
 * @property string $jenis_identitas
 * @property string $no_identitas
 * @property string $jenis_kelamin
 * @property string $status_perkawinan
 * @property string $tanggal_lahir
 * @property string $tempat_lahir
 * @property string $nama_ibu_kandung
 * @property int|null $id_agama
 * @property string|null $suku
 * @property int|null $id_pendidikan
 * @property int|null $id_pekerjaan
 * @property string|null $pekerjaan_lain
 * @property string|null $no_hp
 * @property string|null $no_wa
 * @property string|null $no_hp_alternatif
 * @property string|null $alamat
 * @property string|null $rt
 * @property string|null $rw
 * @property int|null $id_negara
 * @property int|null $id_provinsi
 * @property int|null $id_kabupaten
 * @property int|null $id_kecamatan
 * @property int|null $id_kelurahan_desa
 * @property string|null $no_pegawai
 * @property string|null $jenis_pegawai
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 */
class Staff extends \yii\db\ActiveRecord
{
  use AuditTrailTrait;
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'staff';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['id_agama', 'suku', 'id_pendidikan', 'id_pekerjaan', 'pekerjaan_lain', 'no_hp', 'no_wa', 'no_hp_alternatif', 'alamat', 'rt', 'rw', 'id_negara', 'id_provinsi', 'id_kabupaten', 'id_kecamatan', 'id_kelurahan_desa', 'no_pegawai', 'jenis_pegawai', 'created_by', 'updated_by', 'deleted_by', 'deleted_at'], 'default', 'value' => null],
      [['nama_lengkap', 'jenis_identitas', 'no_identitas', 'jenis_kelamin', 'status_perkawinan', 'tanggal_lahir', 'tempat_lahir', 'nama_ibu_kandung'], 'required'],
      [['tanggal_lahir', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
      [['id_agama', 'id_pendidikan', 'id_pekerjaan', 'id_negara', 'id_provinsi', 'id_kabupaten', 'id_kecamatan', 'id_kelurahan_desa', 'created_by', 'updated_by', 'deleted_by'], 'default', 'value' => null],
      [['id_agama', 'id_pendidikan', 'id_pekerjaan', 'id_negara', 'id_provinsi', 'id_kabupaten', 'id_kecamatan', 'id_kelurahan_desa', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
      [['nama_lengkap', 'jenis_identitas', 'no_identitas', 'jenis_kelamin', 'status_perkawinan', 'tempat_lahir', 'nama_ibu_kandung', 'suku', 'pekerjaan_lain', 'no_hp', 'no_wa', 'no_hp_alternatif', 'alamat', 'no_pegawai', 'jenis_pegawai'], 'string', 'max' => 255],
      [['rt', 'rw'], 'string', 'max' => 5],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id_staff' => 'Id Staff',
      'nama_lengkap' => 'Nama Lengkap',
      'jenis_identitas' => 'Jenis Identitas',
      'no_identitas' => 'No Identitas',
      'jenis_kelamin' => 'Jenis Kelamin',
      'status_perkawinan' => 'Status Perkawinan',
      'tanggal_lahir' => 'Tanggal Lahir',
      'tempat_lahir' => 'Tempat Lahir',
      'nama_ibu_kandung' => 'Nama Ibu Kandung',
      'id_agama' => 'Id Agama',
      'suku' => 'Suku',
      'id_pendidikan' => 'Id Pendidikan',
      'id_pekerjaan' => 'Id Pekerjaan',
      'pekerjaan_lain' => 'Pekerjaan Lain',
      'no_hp' => 'No Hp',
      'no_wa' => 'No Wa',
      'no_hp_alternatif' => 'No Hp Alternatif',
      'alamat' => 'Alamat',
      'rt' => 'Rt',
      'rw' => 'Rw',
      'id_negara' => 'Id Negara',
      'id_provinsi' => 'Id Provinsi',
      'id_kabupaten' => 'Id Kabupaten',
      'id_kecamatan' => 'Id Kecamatan',
      'id_kelurahan_desa' => 'Id Kelurahan Desa',
      'no_pegawai' => 'No Pegawai',
      'jenis_pegawai' => 'Jenis Pegawai',
      'created_by' => 'Created By',
      'updated_by' => 'Updated By',
      'deleted_by' => 'Deleted By',
      'created_at' => 'Created At',
      'updated_at' => 'Updated At',
      'deleted_at' => 'Deleted At',
    ];
  }
  public static function getDataList($query = null)
  {
    $data = static::find()
      ->select(['id_staff', 'nama_lengkap', 'no_identitas', 'no_hp', 'alamat'])
      ->where(['deleted_at' => null])
      ->andFilterWhere([
        'or',
        ['ilike', 'nama_lengkap', $query],
        ['ilike', 'no_identitas', $query],
        ['ilike', 'no_hp', $query],
        ['ilike', 'alamat', $query],
      ])
      ->asArray()
      ->all();

    return array_map(function ($item) {
      return [
        'id' => $item['id_staff'],
        'text' => "{$item['nama_lengkap']} - {$item['no_hp']} - {$item['alamat']}",
      ];
    }, $data);
  }
}
