<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class PenerimaanPembayaran extends ActiveRecord
{
    public $tipe_pembayaran; // untuk disimpan ke model Pemasukan

    public static function tableName()
    {
        return 'penerimaan_pembayaran';
    }

    public function rules()
    {
        return [
            [['pemasukan_id', 'jumlah_terbayar'], 'required'],
            [['pemasukan_id', 'pemasukan_cicilan_id', 'jumlah_terbayar', 'accountkeluar_id', 'potongan_pajak'], 'integer'],
            // [['jumlah_terbayar'], 'number'],
            [['tanggal_bukti_transfer'], 'safe'],
            [['bukti_transfer'], 'file', 'extensions' => ['jpg', 'jpeg', 'png', 'pdf']],
            [['deskripsi'], 'string'],
            [['tipe_pembayaran'], 'string', 'max' => 20],

            [['pemasukan_cicilan_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\PemasukanCicilan::class, 'targetAttribute' => ['pemasukan_cicilan_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'pemasukan_id' => 'Faktur',
            'pemasukan_cicilan_id' => 'Cicilan',
            'accountkeluar_id' => 'Akun Tujuan',
            'bukti_transfer' => 'Bukti Transfer',
            'potongan_pajak' => 'Potongan Pajak',
            'tanggal_bukti_transfer' => 'Tanggal Pembayaran',
            'created_at' => 'Dibuat Pada',
            'updated_at' => 'Diperbarui Pada',
            'tipe_pembayaran' => 'Tipe Pembayaran',
        ];
    }

    public function getPemasukan()
    {
        return $this->hasOne(Pemasukan::class, ['pemasukan_id' => 'pemasukan_id']);
    }

    public function getCicilan()
    {
        return $this->hasOne(PemasukanCicilan::class, ['id' => 'pemasukan_cicilan_id']);
    }

    public function getAccountkeluar()
    {
        return $this->hasOne(Accountkeluar::class, ['id' => 'accountkeluar_id']);
    }
    public function getTanggalBayar()
    {
        return $this->tanggal_bukti_transfer
            ? date("d-m-Y", strtotime($this->tanggal_bukti_transfer))
            : '-';
    }
}
