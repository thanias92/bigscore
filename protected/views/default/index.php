<?php

use yii\helpers\Url;
use yii\db\Query;
use yii\web\JsExpression;
use yii\helpers\Json;

// Array untuk mapping nama hari dalam bahasa Indonesia
$namaHari = [
    'Sunday' => 'Minggu',
    'Monday' => 'Senin',
    'Tuesday' => 'Selasa',
    'Wednesday' => 'Rabu',
    'Thursday' => 'Kamis',
    'Friday' => 'Jumat',
    'Saturday' => 'Sabtu'
];

// Array untuk mapping nama bulan dalam bahasa Indonesia
$namaBulan = [
    'January' => 'Januari',
    'February' => 'Februari',
    'March' => 'Maret',
    'April' => 'April',
    'May' => 'Mei',
    'June' => 'Juni',
    'July' => 'Juli',
    'August' => 'Agustus',
    'September' => 'September',
    'October' => 'Oktober',
    'November' => 'November',
    'December' => 'Desember'
];

// Mendapatkan nama hari dan bulan dalam bahasa Indonesia
$dayOfWeek = date('l', time());
$monthOfYear = date('F', time());

// Mengganti nama hari dan bulan menggunakan array mapping
$hariIndonesia = isset($namaHari[$dayOfWeek]) ? $namaHari[$dayOfWeek] : $dayOfWeek;
$bulanIndonesia = isset($namaBulan[$monthOfYear]) ? $namaBulan[$monthOfYear] : $monthOfYear;

// Tampilkan tanggal dengan format bahasa Indonesia
$tanggalIndonesia = $hariIndonesia . ', ' . date('j') . ' ' . $bulanIndonesia . ' ' . date('Y');

$hariIni = date('l j F Y');

?>
<style>
    .cardhead {
        align-items: center;
        display: grid;
        align-content: center;
        /* flex-direction: column; */
        justify-content: center;
        height: 100%;
    }
</style>
<div class="card">
    <div class="card-header d-flex">
        <div class="header-title flex-grow-1">
            <div class="d-flex justify-content-end align-items-center">
                <span><?= $tanggalIndonesia ?></span>
            </div>
            <div class="row">
                <div class="col-lg-4">
                    <img src="<?php echo  Yii::$app->request->BaseUrl  ?>/themes/img/icon.png" class="img-fluid">
                </div>
                <div class="col-lg-8">
                    <h4 class="card-title">Dashboard</h4>
                    <i class="lead">
                        Selamat Datang <strong class="font-weight-bold"><?= Yii::$app->user->identity->username ?></strong><br>
                        Silahkan gunakan aplikasi <strong class="font-weight-bold">BIGS CORE </strong> sesuai hak akses yang telah diberikan. Jika terjadi permasalahan, Silahkan Hubungi Administrator. </i>

                    <br><br><br><br>
                </div>
            </div>


            <?php
            // dump(Yii::$app->i18n);
            ?>
            <?php //Yii::t('app', 'Submit'); 
            ?>
        </div>
    </div>
</div>