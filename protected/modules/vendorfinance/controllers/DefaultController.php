<?php

namespace app\modules\vendorfinance\controllers;

use Yii;
use yii\web\Controller;
use yii\db\Expression;
use app\models\Pemasukan;
use app\models\Pengeluaran;
use app\models\PemasukanCicilan;

class DefaultController extends Controller
{
  public function actionIndex()
  {
    // Total Pemasukan
    $totalPemasukan = 0;
    $pemasukanList = Pemasukan::find()->where(['deleted_at' => null, 'status' => 'Lunas'])->all();

    foreach ($pemasukanList as $pemasukan) {
      $totalPemasukan += $pemasukan->getTotalPembayaran();
    }

    // Total Pengeluaran
    $totalPengeluaran = Pengeluaran::find()
      ->where(['deleted_at' => null, 'status_pembayaran' => 'Sudah Dibayar'])
      ->sum('jumlah');

    // Tagihan Belum Dibayar
    $tagihanBelumDibayar = 0;
    $semuaPemasukan = Pemasukan::find()
      ->where(['deleted_at' => null])
      ->all();

    foreach ($semuaPemasukan as $pemasukan) {
      $tagihanBelumDibayar += $pemasukan->getSisaTagihan();
    }

    // Produk Terjual (dari relasi berantai: Pemasukan -> Deals -> Product)
    $produkTerjual = [];
    $pemasukanList = Pemasukan::find()
      ->joinWith(['deals.product']) // pastikan relasi ini tersedia
      ->where(['IS NOT', 'deals.product_id', null])
      ->all();

    foreach ($pemasukanList as $row) {
      $namaProduk = $row->deals->product->product_name ?? 'Tidak Diketahui';
      if (!isset($produkTerjual[$namaProduk])) {
        $produkTerjual[$namaProduk] = 0;
      }
      $produkTerjual[$namaProduk]++;
    }

    $analisisPenjualan = [
      'bulan' => [
        'pemasukan' => [],
        'pengeluaran' => [],
      ],
      'tahun' => [
        'pemasukan' => 0,
        'pengeluaran' => 0,
      ],
    ];

    $year = date('Y');
    $totalPemasukanTahunan = 0;
    $totalPengeluaranTahunan = 0;

    for ($i = 1; $i <= 12; $i++) {
      $monthlyPemasukan = Pemasukan::find()
        ->where(new \yii\db\Expression('EXTRACT(MONTH FROM purchase_date) = :month AND EXTRACT(YEAR FROM purchase_date) = :year'))
        ->addParams([':month' => $i, ':year' => $year])
        ->sum('grand_total');

      $monthlyPengeluaran = Pengeluaran::find()
        ->where(new \yii\db\Expression('EXTRACT(MONTH FROM tanggal) = :month AND EXTRACT(YEAR FROM tanggal) = :year'))
        ->addParams([':month' => $i, ':year' => $year])
        ->sum('jumlah');

      $analisisPenjualan['bulan']['pemasukan'][] = (int) $monthlyPemasukan;
      $analisisPenjualan['bulan']['pengeluaran'][] = (int) $monthlyPengeluaran;

      $totalPemasukanTahunan += (int) $monthlyPemasukan;
      $totalPengeluaranTahunan += (int) $monthlyPengeluaran;
    }

    $analisisPenjualan['tahun']['pemasukan'] = array_fill(0, 12, (int) ($totalPemasukanTahunan / 12));
    $analisisPenjualan['tahun']['pengeluaran'] = array_fill(0, 12, (int) ($totalPengeluaranTahunan / 12));


    return $this->render('index', [
      'totalPemasukan'      => $totalPemasukan,
      'totalPengeluaran'    => $totalPengeluaran,
      'tagihanBelumDibayar' => $tagihanBelumDibayar,
      'produkTerjual'       => $produkTerjual,
      'analisisPenjualan'   => $analisisPenjualan,
    ]);
  }
  public function getTotalPembayaran()
  {
    if ($this->is_cicilan) {
      return (float) PemasukanCicilan::find()
        ->where(['pemasukan_id' => $this->id])
        ->sum('jumlah_dibayar');
    }

    return (float) $this->bayar_diterima;
  }
}
