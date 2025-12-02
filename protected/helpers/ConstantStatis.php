<?php

namespace app\helpers;

class ConstantStatis
{
    public const JENIS_KELAMIN = ['Laki-laki' => 'Laki-laki', 'Perempuan' => 'Perempuan'];
    public const JENIS_IDENTITAS = ['NIK' => 'NIK', 'KITAS' => 'KITAS', 'PASSPORT' => 'PASSPORT'];
    public const STATUS_PERKAWINAN = ['1' => 'Belum Kawin', '2' => 'Kawin', '3' => 'Cerai Hidup', '4' => 'Cerai Mati'];
    public const JENIS_PEGAWAI = ['Medis' => 'Medis', 'Non Medis' => 'Non Medis'];
    public const STATUS_PEGAWAI = ['Kontrak' => 'Kontrak', 'Pegawai Tetap' => 'Pegawai Tetap'];
    public const FUNGSIONAL = [
        'Dokter' => 'Dokter',
        'Perawat' => 'Perawat',
        'Analis' => 'Analis',
        'Fisioterapis' => 'Fisioterapis',
        'Ahli Gizi' => 'Ahli Gizi',
        'Bidan' => 'Bidan',
        'Radiografer' => 'Radiografer'
    ];

    public const SHIFT = ['Pagi', 'Siang', 'Sore', 'Malam'];
    public const JENIS_REGISTRASI = ['OTC', 'Rawat Jalan', 'Rawat Inap', 'IGD', 'Penunjang'];
    public const STATUS_TAGIHAN = ['Unbill', 'Bill'];
    public const STATUS_PEMBAYARAN = ['Belum Lunas', 'Lunas'];
    public const JENIS_PERJANJIAN = ['Pasien Baru', 'Pasien Lama'];
    public const ASAL_BOOKING = ['Whatsapp' => 'Whatsapp', 'Telepon' => 'Telepon', 'Website' => 'Website'];
    public const HARI = [
        'Senin' => 'Senin',
        'Selasa' => 'Selasa',
        'Rabu' => 'Rabu',
        'Kamis' => 'Kamis',
        'Jumat' => 'Jumat',
        'Sabtu' => 'Sabtu',
        'Minggu' => 'Minggu',
    ];
    public const JENIS_ASURANSI_PENJAMIN = 66;

    // public const customer_source = [
    //     'satusehat' => 'SATUSEHAT Data',
    //     'bps' => 'Badan Pusat Statistik (BPS)',
    //     'eklinik' => 'eKlinik',
    //     'dinkes' => 'Dinas Kesehatan Riau'
    // ];

    public const label_deals = [
        'new' => 'New',
        'proposalSent' => 'Proposal Sent',
        'negotation' => 'Negotiation',
        'dealWon' => 'Deal Won',
        'dealLost' => 'Deal Lost'
    ];

    public const status_contract = [
        'aktif' => 'Aktif',
        'tidak' => 'Tidak Aktif'
    ];

    public const rate = [
        'Tidak Puas' => 'Tidak Puas',
        'Puas' => 'Puas',
        'Sangat Puas' => 'Sangat Puas'
    ];

    public const send = [
        'customer' => 'Customer',
        'it helpdesk' => 'IT Helpdesk'
    ];

    public const status_payment = [
        'late1' => 'Late(1)',
        'late2' => 'Late(2)',
        'late3' => 'Late(3)'
    ];

    public const status_contract_notification = [
        'active' => 'Active',
        'warning' => 'Warning',
        'expired' => 'Expired',
        'suspend' => 'Suspend'
    ];
}
