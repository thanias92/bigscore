<?php

namespace app\modules\vendorfinance\controllers;

use Yii;
use yii\web\Controller;
use app\models\Pemasukan;
use app\models\Pengeluaran;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\data\ActiveDataProvider;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Mpdf\Mpdf;
use yii\db\Expression;
use yii\web\BadRequestHttpException;
use yii\web\Response;

class LaporanController extends Controller
{
  public function actionIndex()
  {
    return $this->render('index');
  }

  public function actionPemasukan($tahun_awal = null, $bulan_awal = null, $tahun_akhir = null, $bulan_akhir = null, $export = false)
  {
    // Default: Tahun depan jika tidak dipilih
    $tahun_awal = $tahun_awal ?: date('Y', strtotime('+1 year'));
    $bulan_awal = $bulan_awal ?: '01';
    $tahun_akhir = $tahun_akhir ?: $tahun_awal;
    $bulan_akhir = $bulan_akhir ?: '12';

    $start = "{$tahun_awal}-{$bulan_awal}-01";

    // Hitung tanggal akhir dari bulan terakhir
    $endDate = new \DateTime("{$tahun_akhir}-{$bulan_akhir}-01");
    $endDate->modify('last day of this month');
    $end = $endDate->format('Y-m-d');

    $query = (new \yii\db\Query())
      ->select([
        new \yii\db\Expression("TO_CHAR(purchase_date,'YYYY-MM') AS bulan"),
        new \yii\db\Expression("SUM(COALESCE(grand_total, (sub_total + sub_total*0.11 - sub_total*diskon/100))) AS total_pemasukan")
      ])
      ->from('pemasukan')
      ->where(['deleted_at' => null])
      ->andWhere(['status' => 'Lunas'])
      ->andWhere(['between', 'purchase_date', $start, $end])
      ->groupBy(new \yii\db\Expression("TO_CHAR(purchase_date,'YYYY-MM')"))
      ->orderBy(new \yii\db\Expression("TO_CHAR(purchase_date,'YYYY-MM') DESC"))
      ->all();

    // Export
    if ($export === 'pdf') {
      return $this->exportPdf($query, "$tahun_awal-$bulan_awal s.d. $tahun_akhir-$bulan_akhir");
    }
    if ($export === true || $export === '1') {
      return $this->exportExcel($query, "$tahun_awal-$bulan_awal s.d. $tahun_akhir-$bulan_akhir");
    }

    return $this->render('pemasukan', [
      'data' => $query,
      'tahun_awal' => $tahun_awal,
      'bulan_awal' => $bulan_awal,
      'tahun_akhir' => $tahun_akhir,
      'bulan_akhir' => $bulan_akhir,
    ]);
  }


  private function exportExcel(array $data, string $label)
  {
    $spreadsheet = new Spreadsheet();
    $sheet       = $spreadsheet->getActiveSheet();
    $title = "Pemasukan $label";

    // Pastikan maksimal 31 karakter
    $title = substr($title, 0, 31);

    $sheet->setTitle($title);

    /* ── HEADER ─────────────────────────────────────────── */
    $sheet
      ->setCellValue('A1', 'Bulan/Tanggal')
      ->setCellValue('B1', 'No Invoice')
      ->setCellValue('C1', 'Customer')
      ->setCellValue('D1', 'Tipe Transaksi')
      ->setCellValue('E1', 'Status')
      ->setCellValue('F1', 'Sisa Tagihan')
      ->setCellValue('G1', 'Nominal');

    $row   = 2;
    $grand = 0;

    foreach ($data as $rowBulan) {
      $bulanLabel = Yii::$app->formatter->asDate($rowBulan['bulan'] . '-01', 'MMMM yyyy');

      /* ▸ baris ringkasan bulanan */
      $sheet->setCellValue("A{$row}", $bulanLabel);
      $sheet->setCellValue("G{$row}", $rowBulan['total_pemasukan']);
      $sheet->getStyle("A{$row}:G{$row}")->getFont()->setBold(true);
      $grand += $rowBulan['total_pemasukan'];
      $row++;

      /* ▸ detail */
      foreach ($this->getDetailPemasukanByBulan($rowBulan['bulan']) as $det) {
        if (strtolower($det->status) !== 'lunas') {
          continue;
        }

        $tgl = Yii::$app->formatter->asDate($det->purchase_date);
        $customer = $det->deals->customer->customer_name ?? '-';
        $nom = $det->grand_total ?? ($det->sub_total + $det->sub_total * 0.11 - $det->sub_total * $det->diskon / 100);

        $sheet->setCellValue("A{$row}", "  - $tgl");
        $sheet->setCellValue("B{$row}", $det->no_faktur ?: '-');
        $sheet->setCellValue("C{$row}", $customer);
        $sheet->setCellValue("D{$row}", $det->tipe_pembayaran ?? '-'); // ← kamu lupa mengisi kolom D
        $sheet->setCellValue("E{$row}", $det->status ?: '-');
        $sheet->setCellValue("F{$row}", $det->sisa_tagihan ?? 0);
        $sheet->setCellValue("G{$row}", $nom);
        $row++;
      }
    }

    /* ▸ grand total */
    $sheet->setCellValue("F{$row}", 'TOTAL');
    $sheet->setCellValue("G{$row}", $grand);
    $sheet->getStyle("F{$row}:G{$row}")->getFont()->setBold(true);

    /* ── styling ────────────────────────────────────────── */
    $sheet->getStyle('A1:G1')->getFont()->setBold(true);
    $sheet->getStyle("G2:G{$row}")
      ->getNumberFormat()->setFormatCode('#,##0.00');
    $sheet->getStyle("F2:F{$row}")
      ->getNumberFormat()->setFormatCode('#,##0.00');

    foreach (range('A', 'G') as $col) {
      $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    /* ── output ─────────────────────────────────────────── */
    $tmp  = tmpfile();
    $path = stream_get_meta_data($tmp)['uri'];
    (new Xlsx($spreadsheet))->save($path);

    return Yii::$app->response->sendFile(
      $path,
      "Laporan_Pemasukan_{$label}.xlsx",
      ['mimeType' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
    );
  }

  private function exportPdf(array $data, string $label)
  {
    $mpdf = new \Mpdf\Mpdf(['format' => 'A4-L']);

    $html = '
            <style>
              table { border-collapse: collapse; width: 100%; }
              th, td { border: 1px solid #000; padding: 6px; font-size: 11px; text-align: center; }
              th { background-color: #0db2de; color: #fff; }
              td.right { text-align: right; }
            </style>
            
            <table width="100%" style="margin-bottom:10px;">
              <tr>
                <td style="width: 70%;">
                  <h3 style="margin: 0;">PT BIGS INTEGRASI TEKNOLOGI</h3>
                  <strong>DAFTAR PEMASUKAN</strong><br>
                  <small>' . date('d/m/Y') . '</small>
                </td>
                <td style="text-align: right;">
                  <img src="' . Yii::getAlias('@webroot/images/logo-bigs.png') . '" height="60">
                </td>
              </tr>
            </table>
            
            <table>
              <thead>
                <tr>
                  <th>Bulan</th>
                  <th>Tanggal</th>
                  <th>No Faktur</th>
                  <th>Customer</th>
                  <th>Tipe</th>
                  <th>Status</th>
                  <th>Sisa</th>
                  <th>Jumlah</th>
                </tr>
              </thead>
              <tbody>';

    $grand = 0;

    foreach ($data as $rowBulan) {
      $bulanLabel = Yii::$app->formatter->asDate($rowBulan['bulan'] . '-01', 'MMMM yyyy');
      $subtotal   = number_format($rowBulan['total_pemasukan'], 0, ',', '.');
      $grand     += $rowBulan['total_pemasukan'];

      $html .= "<tr style='background:#fafafa;font-weight:bold;'>
              <td>{$bulanLabel}</td>
              <td colspan='6'></td>
              <td class='right'>Rp {$subtotal}</td>
          </tr>";

      $details = $this->getDetailPemasukanByBulan($rowBulan['bulan']);
      $grouped = [];

      foreach ($details as $item) {
        if (strtolower($item->status) !== 'lunas') {
          continue;
        }
        $parentId = $item->parent_id ?: $item->pemasukan_id;
        $grouped[$parentId][] = $item;
      }

      foreach ($grouped as $items) {
        $first = $items[0];
        $invoice = Html::encode($first->no_faktur ?? 'No Invoice');
        $customer = Html::encode($first->deals->customer->customer_name ?? '-');

        // Header kecil per invoice
        $html .= "<tr style='background-color:#cce5ff;font-weight:bold;'>
                        <td colspan='8' style='text-align:left;'>
                          {$invoice} — {$customer}
                        </td>
                      </tr>";

        foreach ($items as $det) {
          $tgl = Yii::$app->formatter->asDate($det->purchase_date);
          $nom = $det->grand_total ?? ($det->sub_total + $det->sub_total * 0.11 - $det->sub_total * $det->diskon / 100);
          $tipe = Html::encode($det->tipe_pembayaran ?? '-');
          $status = Html::encode($det->status ?? '-');
          $customer = Html::encode($first->deals->customer->customer_name ?? '-');
          $jumlah = number_format($nom, 0, ',', '.');

          $html .= "<tr>
                          <td>-</td>
                          <td>{$tgl}</td>
                          <td>" . Html::encode($det->no_faktur ?: '-') . "</td>
                          <td>{$customer}</td> <!-- Kosong karena nama customer sudah di header -->
                          <td>{$tipe}</td>
                          <td>{$status}</td>
                          <td class='right'>Rp {$jumlah}</td>
                        </tr>";
        }
      }
    }

    $html .= "<tr style='font-weight:bold;background:#eaeaea;'>
          <td colspan='7'>Total Keseluruhan</td>
          <td class='right'>Rp " . number_format($grand, 0, ',', '.') . "</td>
      </tr>";

    $html .= '</tbody></table>';

    $mpdf->WriteHTML($html);
    return $mpdf->Output("Laporan_Pemasukan_{$label}.pdf", \Mpdf\Output\Destination::DOWNLOAD);
  }

  private function getDetailPemasukanByBulan(string $bulan)
  {
    $start = $bulan . '-01';
    $end   = date('Y-m-t', strtotime($start));
    return Pemasukan::find()
      ->where(['between', 'purchase_date', $start, $end])
      ->andWhere(['deleted_at' => null])
      ->orderBy(['purchase_date' => SORT_ASC])
      ->all();
  }

  public function actionAjaxDetailPemasukan($tahun = null, $bulan = null)
  {
    if (!$bulan || !preg_match('/^\d{4}-\d{2}$/', $bulan)) {
      throw new BadRequestHttpException('Format bulan salah. Gunakan format YYYY-MM');
    }

    $start = $bulan . '-01';
    $end = date('Y-m-t', strtotime($start));

    Yii::$app->response->format = Response::FORMAT_HTML;

    $pemasukan = \app\models\Pemasukan::find()
      ->alias('p')
      ->where(['p.deleted_at' => null])
      ->andWhere(['p.status' => 'Lunas'])
      ->andWhere(['between', 'p.purchase_date', $start, $end])
      ->orderBy(['p.purchase_date' => SORT_DESC])
      ->all();

    return $this->renderPartial('_detail_pemasukan', [
      'pemasukan' => $pemasukan,
    ]);
  }

  public function actionPengeluaran($tahun_awal = null, $bulan_awal = null, $tahun_akhir = null, $bulan_akhir = null, $export = false)
  {
    $tahun_awal = $tahun_awal ?: date('Y', strtotime('+1 year'));
    $bulan_awal = $bulan_awal ?: '01';
    $tahun_akhir = $tahun_akhir ?: $tahun_awal;
    $bulan_akhir = $bulan_akhir ?: '12';

    $start = "{$tahun_awal}-{$bulan_awal}-01";

    // Hitung tanggal akhir dari bulan terakhir
    $endDate = new \DateTime("{$tahun_akhir}-{$bulan_akhir}-01");
    $endDate->modify('last day of this month');
    $end = $endDate->format('Y-m-d');
    $query = (new \yii\db\Query())
      ->select([
        new \yii\db\Expression("TO_CHAR(tanggal,'YYYY-MM') AS bulan"),
        new \yii\db\Expression("SUM(jumlah) AS total_pengeluaran")
      ])
      ->from('pengeluaran')
      ->where(['deleted_at' => null])
      ->andWhere(['status_pembayaran' => 'Sudah Dibayar'])
      ->andWhere(['between', 'tanggal', $start, $end])
      ->groupBy(new \yii\db\Expression("TO_CHAR(tanggal,'YYYY-MM')"))
      ->orderBy(new \yii\db\Expression("TO_CHAR(tanggal,'YYYY-MM') DESC"))
      ->all();

    if ($export === 'pdf') {
      return $this->exportPdfPengeluaran($query, "$tahun_awal-$bulan_awal s.d. $tahun_akhir-$bulan_akhir");
    }
    if ($export === true || $export === '1') {
      return $this->exportExcelPengeluaran($query, "$tahun_awal-$bulan_awal s.d. $tahun_akhir-$bulan_akhir");
    }

    return $this->render('pengeluaran', [
      'data' => $query,
      'tahun_awal' => $tahun_awal,
      'bulan_awal' => $bulan_awal,
      'tahun_akhir' => $tahun_akhir,
      'bulan_akhir' => $bulan_akhir,
    ]);
  }

  private function exportExcelPengeluaran(array $data, string $label)
  {
    $spreadsheet = new Spreadsheet();
    $sheet       = $spreadsheet->getActiveSheet();
    $title = "Pengeluaran $label";

    // Header
    $sheet->setCellValue('A1', 'Tanggal');
    $sheet->setCellValue('B1', 'Nomor');
    $sheet->setCellValue('C1', 'Kategori');
    $sheet->setCellValue('D1', 'Deskripsi');
    $sheet->setCellValue('E1', 'Supplier');
    $sheet->setCellValue('F1', 'Jumlah');
    $sheet->setCellValue('G1', 'Status');


    $row = 2;
    $total = 0;

    foreach ($data as $rowBulan) {
      $details = $this->getDetailPengeluaranByBulan($rowBulan['bulan']);
      foreach ($details as $det) {
        $tanggal = Yii::$app->formatter->asDate($det->tanggal);
        $nomor = $det->no_pengeluaran ?: '-';
        $kategori = $det->accountkeluar
          ? $det->accountkeluar->akun . ' (' . $det->accountkeluar->code . ')'
          : '-';
        $deskripsi = $det->keterangan ?: '-';
        $supplier = $det->vendor->nama_vendor ?? '-';
        $jumlah = (float) $det->jumlah;

        $sheet->setCellValue("A{$row}", $tanggal);
        $sheet->setCellValue("B{$row}", $nomor);
        $sheet->setCellValue("C{$row}", $kategori);
        $sheet->setCellValue("D{$row}", $deskripsi);
        $sheet->setCellValue("E{$row}", $supplier);
        $sheet->setCellValue("F{$row}", $jumlah);
        $sheet->setCellValue("G{$row}", $det->status_pembayaran ?: '-');


        $row++;
        $total += $jumlah;
      }
    }

    // Total row
    $sheet->setCellValue("F{$row}", 'Total Biaya');
    $sheet->setCellValue("G{$row}", $total);

    // Format header bold
    $sheet->getStyle('A1:G1')->getFont()->setBold(true);
    $sheet->getStyle("F{$row}:G{$row}")->getFont()->setBold(true);
    $sheet->getStyle("G2:G{$row}")
      ->getNumberFormat()
      ->setFormatCode('#,##0.00');

    foreach (range('A', 'G') as $col) {
      $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    $tmp = tmpfile();
    $path = stream_get_meta_data($tmp)['uri'];
    (new Xlsx($spreadsheet))->save($path);
    return Yii::$app->response->sendFile(
        $path,
        "Laporan_Pengeluaran_{$label}.xlsx",
        [
          'mimeType' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ]
      );
  }

  private function exportPdfPengeluaran(array $data, string $label)
  {
    $mpdf = new \Mpdf\Mpdf(['format' => 'A4-L']);

    $html = '
      <style>
          body { font-family: sans-serif; }
          table { border-collapse: collapse; width: 100%; }
          th, td { border: 1px solid #000; padding: 5px; font-size: 11px; }
          th { background-color: #0db2de; color: #fff; text-align: center; }
          td.right { text-align: right; }
          td.center { text-align: center; }
      </style>
      <table width="100%" style="margin-bottom:10px;">
          <tr>
              <td style="width: 70%;">
                  <h3 style="margin: 0;">PT BIGS INTEGRASI TEKNOLOGI</h3>
                  <strong>DAFTAR PENGELUARAN</strong><br>
                  <small>' . date('d/m/Y') . '</small>
              </td>
              <td style="text-align: right;">
                  <img src="' . Yii::getAlias('@webroot/images/logo-bigs.png') . '" height="60">
              </td>
          </tr>
      </table>
      <table>
          <thead>
              <tr>
                  <th>Tanggal</th>
                  <th>Nomor</th>
                  <th>Kategori</th>
                  <th>Deskripsi</th>
                  <th>Supplier</th>
                  <th>Status</th>
                  <th class="right">Jumlah</th>
              </tr>
          </thead>
          <tbody>';

    $total = 0;

    foreach ($data as $rowBulan) {
      $detailList = $this->getDetailPengeluaranByBulan($rowBulan['bulan']);
      foreach ($detailList as $det) {
        $tanggal   = Yii::$app->formatter->asDate($det->tanggal);
        $nomor     = $det->no_pengeluaran ?: '-';
        $kategori  = $det->accountkeluar
          ? $det->accountkeluar->akun . ' (' . $det->accountkeluar->code . ')'
          : '-';
        $deskripsi = $det->keterangan ?: '-';
        $supplier  = $det->vendor->nama_vendor ?? '-';
        $status    = $det->status_pembayaran ?: '-';
        $jumlah    = (float) $det->jumlah;

        $html .= '<tr>
                <td class="center">' . $tanggal . '</td>
                <td class="center">' . $nomor . '</td>
                <td>' . $kategori . '</td>
                <td>' . $deskripsi . '</td>
                <td>' . $supplier . '</td>
                <td class="center">' . $status . '</td>
                <td class="right">' . number_format($jumlah, 2, ',', '.') . '</td>
            </tr>';

        $total += $jumlah;
      }
    }

    $html .= '
        <tr style="font-weight:bold; background:#f2f2f2;">
            <td colspan="6" class="right">Total Biaya</td>
            <td class="right">' . number_format($total, 2, ',', '.') . '</td>
        </tr>
        </tbody>
    </table>';

    $mpdf->WriteHTML($html);
    return $mpdf->Output("Laporan_Pengeluaran_{$label}.pdf", \Mpdf\Output\Destination::DOWNLOAD);
  }

  private function getDetailPengeluaranByBulan(string $bulan)
  {
    if (!preg_match('/^\d{4}-\d{2}$/', $bulan)) {
      throw new \yii\web\BadRequestHttpException('Format bulan salah');
    }

    $start = $bulan . '-01';
    $end   = date('Y-m-t', strtotime($start));

    return Pengeluaran::find()
      ->andWhere(['between', 'tanggal', $start, $end])
      ->andWhere(['deleted_at' => null])
      ->andWhere(['status_pembayaran' => 'Sudah Dibayar'])
      ->orderBy(['tanggal' => SORT_DESC])
      ->all();
  }

  public function actionAjaxDetailPengeluaran($bulan)
  {
    if (!preg_match('/^\d{4}-\d{2}$/', $bulan)) {
      throw new \yii\web\BadRequestHttpException('Format bulan salah');
    }

    $pengeluaran = $this->getDetailPengeluaranByBulan($bulan);

    Yii::$app->response->format = \yii\web\Response::FORMAT_HTML;
    return $this->renderPartial('_detail_pengeluaran', [
      'pengeluaran' => $pengeluaran,
    ]);
  }

  public function actionArusKas($export = null, $tahun = null)
  {
    // Ambil parameter rentang bulan dari request
    $bulan_dari = Yii::$app->request->get('bulan_dari');
    $bulan_hingga = Yii::$app->request->get('bulan_hingga');
    

    // Ambil seluruh tahun dari pemasukan dan pengeluaran
    $tahunList = (new \yii\db\Query())
      ->select([new \yii\db\Expression("DISTINCT EXTRACT(YEAR FROM purchase_date) AS th")])
      ->from('pemasukan')
      ->where(['deleted_at' => null])
      ->union(
        (new \yii\db\Query())
          ->select([new \yii\db\Expression("DISTINCT EXTRACT(YEAR FROM tanggal) AS th")])
          ->from('pengeluaran')
          ->where(['deleted_at' => null])
      )
      ->column();

    rsort($tahunList); // urutkan dari terbaru

    // Siapkan data tahunan
    $data = [];
    foreach ($tahunList as $th) {
      $kasAwal = $this->getKasAwal($th); // ← Pindahkan ke sini

      $masuk = (float) Pemasukan::find()
        ->where(['deleted_at' => null])
        ->andWhere(['status' => 'Lunas']) // hanya yang sudah lunas
        ->andWhere(['between', 'purchase_date', "$th-01-01", "$th-12-31"])
        ->sum('grand_total') ?: 0;

      $keluar = (float) Pengeluaran::find()
        ->where(['deleted_at' => null])
        ->andWhere(['status_pembayaran' => 'Sudah Dibayar'])
        ->andWhere(['between', 'tanggal', "$th-01-01", "$th-12-31"])
        ->sum('jumlah') ?: 0;

      $data[] = [
        'tahun'  => (int) $th,
        'masuk'  => $masuk,
        'keluar' => $keluar,
        'saldo'  => $kasAwal + $masuk - $keluar, // hanya 1 key saldo
      ];
    }

    // Handle ekspor Excel
    if ($export === 'excel-detail') {
      if ($tahun) {
        $filtered = array_filter($data, fn($d) => $d['tahun'] == $tahun);
        return $this->exportExcelArusKasDetail(array_values($filtered), $tahun, $bulan_dari ?? 1, $bulan_hingga ?? 12);
      }
      return $this->exportExcelArusKasDetail($data);
    }

    // Handle ekspor PDF
    if ($export === 'pdf-detail') {
      if ($tahun) {
        $filtered = array_filter($data, fn($d) => $d['tahun'] == $tahun);
        return $this->exportPdfArusKasDetail(array_values($filtered), $tahun, $bulan_dari ?? 1, $bulan_hingga ?? 12);
      }
      return $this->exportPdfArusKasDetail($data);
    }

    // Tampilkan halaman utama
    return $this->render('arus_kas', [
      'data' => $data,
    ]);
  }

  public function actionAjaxDetailArusKas($tahun)
  {
    // Validasi
    if (!preg_match('/^\d{4}$/', $tahun)) {
      throw new \yii\web\BadRequestHttpException('Format tahun salah');
    }

    $bulanList = [];                                 // ← hasil akhir

    /* ---------- loop 12 bulan ---------- */
    for ($i = 1; $i <= 12; $i++) {
      $bulan    = str_pad($i, 2, '0', STR_PAD_LEFT);          // 01‑12
      $lastDay  = date('t', strtotime("$tahun-$bulan-01"));   // 28‑31
      $label    = Yii::$app->formatter->asDate("$tahun-$bulan-01", 'MMMM yyyy');

      /* ambil pemasukan */
      $pemasukan = (new \yii\db\Query())
        ->select(['purchase_date AS tgl', 'grand_total AS nominal'])
        ->from('pemasukan')
        ->where(['deleted_at' => null, 'status' => 'Lunas']) // untuk pemasukan
        ->andWhere(['between', 'purchase_date', "$tahun-$bulan-01", "$tahun-$bulan-$lastDay"])
        ->all();

      /* ambil pengeluaran */
      $pengeluaran = (new \yii\db\Query())
        ->select(['tanggal AS tgl', 'jumlah AS nominal'])
        ->from('pengeluaran')
        ->where(['deleted_at' => null, 'status_pembayaran' => 'Sudah Dibayar']) // untuk pengeluaran
        ->andWhere(['between', 'tanggal', "$tahun-$bulan-01", "$tahun-$bulan-$lastDay"])
        ->all();

      /* rekap harian */
      $detail = [];                                            // [Y‑m‑d => ['masuk'=>…, 'keluar'=>…]]
      foreach ($pemasukan as $p) {
        $k = $p['tgl'];
        $detail[$k]['masuk']  = ($detail[$k]['masuk']  ?? 0) + (float) $p['nominal'];
        $detail[$k]['keluar'] = $detail[$k]['keluar'] ?? 0;
      }
      foreach ($pengeluaran as $pg) {
        $k = $pg['tgl'];
        $detail[$k]['keluar'] = ($detail[$k]['keluar'] ?? 0) + (float) $pg['nominal'];
        $detail[$k]['masuk']  = $detail[$k]['masuk']  ?? 0;
      }
      ksort($detail);

      // jumlah bulanan
      $totalMasuk  = array_sum(array_column($detail, 'masuk'));
      $totalKeluar = array_sum(array_column($detail, 'keluar'));

      /* push ke list */
      $bulanList[] = [
        'label' => $label,
        'data'  => $detail,
        'masuk' => array_sum(array_column($detail, 'masuk')),
        'keluar' => array_sum(array_column($detail, 'keluar')),
        'saldo' => array_sum(array_map(fn($d) => $d['masuk'] - $d['keluar'], $detail)),
        'tahun' => $tahun,
        'bulan' => $bulan,
      ];
    }

    Yii::$app->response->format = \yii\web\Response::FORMAT_HTML;
    return $this->renderAjax('_detail_arus_kas', [
      'bulanList' => $bulanList,
    ]);
  }

  public function actionAjaxDetailHarian($tahun, $bulan)
  {
    Yii::$app->response->format = \yii\web\Response::FORMAT_HTML;

    $lastDay = date('t', strtotime("$tahun-$bulan-01"));

    $pemasukan = (new \yii\db\Query())
      ->select(['purchase_date AS tgl', 'grand_total AS nominal'])
      ->from('pemasukan')
      ->where(['deleted_at' => null, 'status' => 'Lunas'])
      ->andWhere(['between', 'purchase_date', "$tahun-$bulan-01", "$tahun-$bulan-$lastDay"])
      ->all();

    $pengeluaran = (new \yii\db\Query())
      ->select(['tanggal AS tgl', 'jumlah AS nominal'])
      ->from('pengeluaran')
      ->where(['deleted_at' => null, 'status_pembayaran' => 'Sudah Dibayar'])
      ->andWhere(['between', 'tanggal', "$tahun-$bulan-01", "$tahun-$bulan-$lastDay"])
      ->all();

    $data = [];

    foreach ($pemasukan as $p) {
      $tgl = $p['tgl'];
      $data[$tgl]['masuk'] = ($data[$tgl]['masuk'] ?? 0) + $p['nominal'];
      $data[$tgl]['keluar'] = $data[$tgl]['keluar'] ?? 0;
    }

    foreach ($pengeluaran as $k) {
      $tgl = $k['tgl'];
      $data[$tgl]['keluar'] = ($data[$tgl]['keluar'] ?? 0) + $k['nominal'];
      $data[$tgl]['masuk'] = $data[$tgl]['masuk'] ?? 0;
    }

    ksort($data);

    return $this->renderAjax('_detail_harian', ['data' => $data]);
  }

  private function exportPdfArusKasDetail(array $ringkasan, $tahun = null, $bulanDari = 1, $bulanHingga = 12)
  {
    $mpdf = new \Mpdf\Mpdf(['format' => 'A4-L']);
    $grandKasAwal = 0;
    // Format nama bulan
    $namaBulan = fn($bulan) => ucfirst(\DateTime::createFromFormat('!m', $bulan)->format('F'));
    $periode = "{$namaBulan($bulanDari)} - {$namaBulan($bulanHingga)} {$tahun}";

    // Ambil logo dari pengaturan akun
    $pengaturan = \app\models\Pengaturanakun::findOne(1);
    $logoPath = Yii::getAlias('@webroot/uploads/logo/' . $pengaturan->logo);
    $logoBase64 = '';

    if (is_file($logoPath)) {
      $logoType = pathinfo($logoPath, PATHINFO_EXTENSION);
      $logoData = file_get_contents($logoPath);
      $logoBase64 = 'data:image/' . $logoType . ';base64,' . base64_encode($logoData);
    }

    ob_start(); // Start buffer
?>
    <style>
      table {
        border-collapse: collapse;
        width: 100%;
      }

      th,
      td {
        border: 1px solid #000;
        padding: 5px;
        font-size: 10px;
      }

      th {
        background: #0db2de;
        color: #fff;
        text-align: center;
      }

      td.right {
        text-align: right;
      }
    </style>

    <table style="margin-bottom:10px; border: none;">
      <tr>
        <td style="border:none">
          <h3 style="margin:0">PT BIGS INTEGRASI TEKNOLOGI</h3>
          <strong>LAPORAN ARUS KAS DETAIL</strong><br>
          <small>Periode: <?= $periode ?></small>
        </td>
        <td style="border:none; text-align:right">
          <?php if ($logoBase64): ?>
            <img src="<?= $logoBase64 ?>" height="50">
          <?php endif; ?>
        </td>
      </tr>
    </table>
    <?php

    $grandMasuk = $grandKeluar = 0;

    foreach ($ringkasan as $rekap) {
      $grandKasAwal += $this->getKasAwal($tahun);
      $tahun = $rekap['tahun'];
      $detail = $this->getArusKasDetailPerTahun($tahun, true, $bulanDari, $bulanHingga);

      echo "<h4 style='margin-top:15px;'>Tahun {$tahun}</h4>";
      echo "<table>
                  <thead>
                    <tr>
                      <th>Bulan / Tanggal</th>
                      <th class='right'>Pemasukan</th>
                      <th class='right'>Pengeluaran</th>
                      <th class='right'>Saldo</th>
                    </tr>
                  </thead>
                  <tbody>";

      foreach ($detail as $d) {
        echo "<tr style='font-weight:bold;background:#fafafa;'>
                      <td>{$d['label']}</td>
                      <td class='right'>" . ($d['label'] === 'Kas Awal' ? '-' : 'Rp ' . number_format($d['masuk'], 0, ',', '.')) . "</td>
                      <td class='right'>" . ($d['label'] === 'Kas Awal' ? '-' : 'Rp ' . number_format($d['keluar'], 0, ',', '.')) . "</td>
                      <td class='right'>Rp " . number_format($d['saldo'], 0, ',', '.') . "</td>
                    </tr>";

        if ($d['label'] !== 'Kas Awal') {
          $grandMasuk += $d['masuk'];
          $grandKeluar += $d['keluar'];

          foreach ($d['harian'] as $tgl => $h) {
            echo "<tr>
                              <td style='padding-left:20px;'>- " . Yii::$app->formatter->asDate($tgl) . "</td>
                              <td class='right'>Rp " . number_format($h['masuk'], 0, ',', '.') . "</td>
                              <td class='right'>Rp " . number_format($h['keluar'], 0, ',', '.') . "</td>
                              <td class='right'>Rp " . number_format($h['masuk'] - $h['keluar'], 0, ',', '.') . "</td>
                            </tr>";
          }
        }
      }

      echo "</tbody></table>";
    }

    // Rekap akhir
    echo "<h4 style='margin-top:25px;'>Rekap Keseluruhan</h4>
            <table style='width:60%'>
              <tr style='background:#f2f2f2;font-weight:bold;'>
                <td>Total Pemasukan</td>
                <td class='right'>Rp " . number_format($grandMasuk, 0, ',', '.') . "</td>
              </tr>
              <tr style='background:#f2f2f2;font-weight:bold;'>
                <td>Total Pengeluaran</td>
                <td class='right'>Rp " . number_format($grandKeluar, 0, ',', '.') . "</td>
              </tr>
              <tr style='background:#eaeaea;font-weight:bold;'>
                <td>Saldo Akhir</td>
                <td class='right'>Rp " . number_format($grandKasAwal + $grandMasuk - $grandKeluar, 0, ',', '.') . "</td>
              </tr>
            </table>";

    $html = ob_get_clean(); // ambil isi buffer
    $mpdf->WriteHTML($html);
    return $mpdf->Output("Arus_Kas_Detail_{$tahun}_{$bulanDari}_to_{$bulanHingga}.pdf", 'D');
  }

  private function exportExcelArusKasDetail(array $ringkasan, $tahun = null, $bulanDari = 1, $bulanHingga = 12)
  {
    $spread = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $spread->removeSheetByIndex(0);
  
    $grandKasAwal = 0;
    $grandMasuk = 0;
    $grandKeluar = 0;
  
    $namaBulan = function ($bulan) {
      return ucfirst(\DateTime::createFromFormat('!m', $bulan)->format('F'));
    };
    $periode = "{$namaBulan($bulanDari)} - {$namaBulan($bulanHingga)} {$tahun}";
  
    foreach ($ringkasan as $rekap) {
      $tahun = $rekap['tahun'];
      $detail = $this->getArusKasDetailPerTahun($tahun, true, $bulanDari, $bulanHingga);
      $kasAwal = $this->getKasAwal($tahun);
      $grandKasAwal += $kasAwal;
  
      $sheet = $spread->createSheet();
      $sheet->setTitle((string)$tahun);
  
      $sheet->setCellValue('A1', 'Laporan Arus Kas Detail');
      $sheet->setCellValue('A2', "Periode: {$periode}");
      $sheet->mergeCells('A1:D1');
      $sheet->mergeCells('A2:D2');
      $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
      $sheet->getStyle('A2')->getFont()->setItalic(true);
  
      $sheet->fromArray(['Bulan / Tanggal', 'Pemasukan', 'Pengeluaran', 'Saldo'], null, 'A3');
      $sheet->freezePane('A4');
      $row = 4;
      $tMasuk = $tKeluar = 0;
  
      foreach ($detail as $d) {
        $sheet->setCellValue("A{$row}", $d['label'])
          ->setCellValue("B{$row}", $d['label'] === 'Kas Awal' ? '' : $d['masuk'])
          ->setCellValue("C{$row}", $d['label'] === 'Kas Awal' ? '' : $d['keluar'])
          ->setCellValue("D{$row}", $d['saldo']);
        $sheet->getStyle("A{$row}:D{$row}")->getFont()->setBold(true);
        $row++;
  
        if ($d['label'] !== 'Kas Awal') {
          $tMasuk += $d['masuk'];
          $tKeluar += $d['keluar'];
  
          foreach ($d['harian'] as $tgl => $h) {
            $sheet->setCellValue("A{$row}", '     - ' . Yii::$app->formatter->asDate($tgl))
              ->setCellValue("B{$row}", $h['masuk'])
              ->setCellValue("C{$row}", $h['keluar'])
              ->setCellValue("D{$row}", $h['masuk'] - $h['keluar']);
            $row++;
          }
        }
      }
  
      $sheet->setCellValue("A{$row}", 'TOTAL Pemasukan')->setCellValue("B{$row}", $tMasuk);
      $sheet->getStyle("A{$row}:B{$row}")->getFont()->setBold(true);
      $row++;
  
      $sheet->setCellValue("A{$row}", 'TOTAL Pengeluaran')->setCellValue("C{$row}", $tKeluar);
      $sheet->getStyle("A{$row}:C{$row}")->getFont()->setBold(true);
      $row++;
  
      $sheet->setCellValue("A{$row}", 'SALDO AKHIR')->setCellValue("D{$row}", $kasAwal + $tMasuk - $tKeluar);
      $sheet->getStyle("A{$row}:D{$row}")->getFont()->setBold(true);
  
      $last = $row;
      $sheet->getStyle("B4:D{$last}")->getNumberFormat()->setFormatCode('#,##0');
      $sheet->getStyle("B4:D{$last}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
  
      foreach (['A', 'B', 'C', 'D'] as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
      }
  
      // Akumulasi global
      $grandMasuk += $tMasuk;
      $grandKeluar += $tKeluar;
    }
  
    // Tambahkan sheet "Rekap"
    $rekapSheet = $spread->createSheet();
    $rekapSheet->setTitle('Rekap');
  
    $rekapSheet->setCellValue('A1', 'Rekap Keseluruhan Arus Kas');
    $rekapSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    $rekapSheet->mergeCells('A1:B1');
  
    $rekapSheet->fromArray([
      ['Total Kas Awal', $grandKasAwal],
      ['Total Pemasukan', $grandMasuk],
      ['Total Pengeluaran', $grandKeluar],
      ['Saldo Akhir', $grandKasAwal + $grandMasuk - $grandKeluar]
    ], null, 'A3');
  
    $rekapSheet->getStyle('A3:B6')->getFont()->setBold(true);
    $rekapSheet->getStyle('B3:B6')->getNumberFormat()->setFormatCode('#,##0');
    $rekapSheet->getStyle('B3:B6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
  
    foreach (['A', 'B'] as $col) {
      $rekapSheet->getColumnDimension($col)->setAutoSize(true);
    }
  
    // Buat file
    $tmp = tmpfile();
    $path = stream_get_meta_data($tmp)['uri'];
    (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spread))->save($path);
  
    return Yii::$app->response->sendFile(
      $path,
      "Arus_Kas_Detail_{$tahun}_{$bulanDari}_to_{$bulanHingga}.xlsx",
      ['mimeType' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
    );
  }
  

  private function getArusKasDetailPerTahun(int $tahun, bool $includeKasAwal = true, int $bulanDari = 1, int $bulanHingga = 12): array
  {
    $detailTahun  = [];
    $runningSaldo = 0;

    /* ── baris Kas Awal ──────────────────────────────────────────────── */
    if ($includeKasAwal) {
      $kasAwal      = $this->getKasAwal($tahun);
      $runningSaldo = $kasAwal;
      $detailTahun[] = [
        'label'  => 'Kas Awal',
        'harian' => [],
        'masuk'  => 0,
        'keluar' => 0,
        'saldo'  => $runningSaldo,
      ];
    }

    /* ── loop 12 bulan ──────────────────────────────────────────────── */
    for ($m = $bulanDari; $m <= $bulanHingga; $m++) {
      $bulan   = str_pad($m, 2, '0', STR_PAD_LEFT);
      $lastDay = date('t', strtotime("$tahun-$bulan-01"));
      $label   = Yii::$app->formatter->asDate("$tahun-$bulan-01", 'MMMM yyyy');

      /* ambil harian pemasukan */
      $pm = (new \yii\db\Query())
        ->select(['DATE(purchase_date) AS tgl', 'SUM(grand_total) AS nom'])
        ->from('pemasukan')
        ->where(['deleted_at' => null])
        ->andWhere(['between', 'purchase_date', "$tahun-$bulan-01", "$tahun-$bulan-$lastDay"])
        ->andWhere(['status' => 'Lunas'])
        ->groupBy('tgl')->all();

      /* ambil harian pengeluaran */
      $pk = (new \yii\db\Query())
        ->select(['DATE(tanggal) AS tgl', 'SUM(jumlah) AS nom'])
        ->from('pengeluaran')
        ->where(['deleted_at' => null])
        ->andWhere(['between', 'tanggal', "$tahun-$bulan-01", "$tahun-$bulan-$lastDay"])
        ->andWhere(['status_pembayaran' => 'Sudah Dibayar'])
        ->groupBy('tgl')->all();

      /* gabung ke array [tgl => [masuk, keluar]] */
      $harian = [];
      foreach ($pm as $r) {
        $tgl = $r['tgl'];
        $harian[$tgl]['masuk']  = (float) $r['nom'];
      }
      foreach ($pk as $r) {
        $tgl = $r['tgl'];
        $harian[$tgl]['keluar'] = (float) $r['nom'];
      }
      foreach ($harian as &$h) {           // normalisasi key
        $h['masuk']  = $h['masuk']  ?? 0;
        $h['keluar'] = $h['keluar'] ?? 0;
      }
      ksort($harian);

      /* rekap bulan + update running saldo */
      $totalMasuk  = array_sum(array_column($harian, 'masuk'));
      $totalKeluar = array_sum(array_column($harian, 'keluar'));
      $runningSaldo += $totalMasuk - $totalKeluar;

      $detailTahun[] = [
        'label'  => $label,
        'harian' => $harian,
        'masuk'  => $totalMasuk,
        'keluar' => $totalKeluar,
        'saldo'  => $runningSaldo,
      ];
    }

    return $detailTahun;
  }

  private function getKasAwal(int $tahun): float
  {
    $masuk  = (float) Pemasukan::find()
      ->where(['deleted_at' => null, 'status' => 'Lunas'])
      ->andWhere(['<', 'purchase_date', "$tahun-01-01"])
      ->sum('grand_total');
    $keluar = (float) Pengeluaran::find()
      ->where(['deleted_at' => null, 'status_pembayaran' => 'Sudah Dibayar'])
      ->andWhere(['<', 'tanggal', "$tahun-01-01"])
      ->sum('jumlah');

    return $masuk - $keluar;     // saldo akhir s.d. 31‑12‑(tahun‑1)
  }

  public function actionLabaRugi($tahun = null, $export = null)
  {
    $tahun = $tahun ?: date('Y');
    $bulan_dari = Yii::$app->request->get('bulan_dari', 1);
    $bulan_hingga = Yii::$app->request->get('bulan_hingga', 12);

    $data = $this->getLabaRugiData($tahun, $bulan_dari, $bulan_hingga);

    if ($export === 'pdf') {
      return $this->exportPdfLabaRugi($data, $tahun, $bulan_dari, $bulan_hingga);
    }

    if ($export === 'excel') {
      return $this->exportExcelLabaRugi($data, $tahun, $bulan_dari, $bulan_hingga);
    }

    return $this->render('laba_rugi', [
      'data' => $data,
      'tahun' => $tahun,
      'bulan_dari' => $bulan_dari,
      'bulan_hingga' => $bulan_hingga,
    ]);
  }

  private function getLabaRugiData(int $tahun, int $bulan_dari = 1, int $bulan_hingga = 12): array
  {

    $start = date("$tahun-$bulan_dari-01");
    $end   = date("Y-m-t", strtotime("$tahun-$bulan_hingga-01")); // akhir bulan terakhir

    $rows = (new \yii\db\Query())
      ->select([
        'kode' => 'COALESCE(a.code, \'\')',
        'nama' => 'COALESCE(a.akun, \'\')',
        'type' => 'COALESCE(a.penggunaan, \'pengeluaran\')',
        'nominal' => new \yii\db\Expression("
          CASE WHEN a.penggunaan = 'pemasukan'
               THEN COALESCE(p.grand_total,0)
               ELSE COALESCE(pg.jumlah,0) END
      ")
      ])
      ->from(['a' => 'accountkeluar'])
      ->leftJoin(['p' => 'pemasukan'], "
      p.accountkeluar_id = a.id 
      AND p.deleted_at IS NULL 
      AND p.purchase_date BETWEEN :s AND :e 
      AND p.status = 'Lunas'
        ", [':s' => $start, ':e' => $end])
      ->leftJoin(['pg' => 'pengeluaran'], "
            pg.accountkeluar_id = a.id 
            AND pg.deleted_at IS NULL 
            AND pg.tanggal BETWEEN :s AND :e 
            AND pg.status_pembayaran = 'Sudah Dibayar'
        ")
      ->andWhere(['a.deleted_at' => null])
      ->all();

    $mapSection = [
      '4' => 'Pendapatan',
      '5' => 'Beban Pokok Pendapatan',
      '6' => 'Beban Operasional',
      '7' => 'Pendapatan / Beban Lain-lain',
      '8' => 'Beban Lain-lain',
    ];

    $result = [];
    foreach ($rows as $r) {
      if (!$r['kode']) continue;

      $sectionKey = substr($r['kode'], 0, 1);
      $section = $mapSection[$sectionKey] ?? 'Lain-lain';

      $kode = $r['kode'];
      $nama = $r['nama'];
      $nominal = (float)$r['nominal'];

      // Inisialisasi array jika belum ada
      if (!isset($result[$section]['detail'][$kode])) {
        $result[$section]['detail'][$kode] = [
          'kode' => $kode,
          'nama' => $nama,
          'nom'  => 0,
        ];
      }

      // Tambahkan nominal ke akun yang sama
      $result[$section]['detail'][$kode]['nom'] += $nominal;

      // Total per section
      $result[$section]['total'] = ($result[$section]['total'] ?? 0) + $nominal;
    }

    $pendapatan   = $result['Pendapatan']['total'] ?? 0;
    $hpp          = $result['Beban Pokok Pendapatan']['total'] ?? 0;
    $operasional  = $result['Beban Operasional']['total'] ?? 0;
    $lainlain     = ($result['Pendapatan / Beban Lain-lain']['total'] ?? 0) +
      ($result['Beban Lain-lain']['total'] ?? 0);

    $result['Laba Kotor']  = $pendapatan - $hpp;
    $result['Laba Bersih'] = $pendapatan - $hpp - $operasional + $lainlain;

    return $result;
  }

  private function exportPdfLabaRugi(array $data, int $tahun, int $bulan_dari = 1, int $bulan_hingga = 12)
  {
    $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);

    $namaBulan = fn($bulan) => ucfirst(\DateTime::createFromFormat('!m', $bulan)->format('F'));
    $periode = "{$namaBulan($bulan_dari)} - {$namaBulan($bulan_hingga)} {$tahun}";

    // Ambil data logo dari Pengaturanakun
    $pengaturan = \app\models\Pengaturanakun::findOne(1);
    $logoPath = Yii::getAlias('@webroot/uploads/logo/' . $pengaturan->logo);
    $logoBase64 = '';

    if (is_file($logoPath)) {
      $logoType = pathinfo($logoPath, PATHINFO_EXTENSION);
      $logoData = file_get_contents($logoPath);
      $logoBase64 = 'data:image/' . $logoType . ';base64,' . base64_encode($logoData);
    }

    ob_start(); // Mulai output buffering
    ?>
    <style>
      body {
        font-family: sans-serif;
        font-size: 11px;
      }

      table {
        width: 100%;
        border-collapse: collapse;
      }

      th,
      td {
        padding: 6px;
        border: 1px solid #000;
      }

      th {
        background: #0db2de;
        color: #fff;
        text-align: center;
      }

      td.right {
        text-align: right;
      }

      .bold {
        font-weight: bold;
      }
    </style>

    <table style="margin-bottom:10px; border: none;">
      <tr>
        <td style="border:none">
          <h3 style="margin:0">PT BIGS INTEGRASI TEKNOLOGI</h3>
          <strong>LAPORAN LABA RUGI</strong><br>
          <small><?= $periode ?></small>
        </td>
        <td style="border:none; text-align:right">
          <?php if ($logoBase64): ?>
            <img src="<?= $logoBase64 ?>" height="50">
          <?php endif; ?>
        </td>
      </tr>
    </table>

    <table>
      <thead>
        <tr>
          <th style="width:20%">Kode</th>
          <th style="width:60%">Keterangan</th>
          <th style="width:20%">Jumlah (Rp)</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($data as $section => $row): ?>
          <?php if (!is_array($row) || !isset($row['detail'])) continue; ?>
          <tr class="bold">
            <td colspan="3"><?= $section ?></td>
          </tr>
          <?php foreach ($row['detail'] as $d): ?>
            <tr>
              <td><?= Html::encode($d['kode']) ?></td>
              <td><?= Html::encode($d['nama']) ?></td>
              <td class="right"><?= number_format($d['nom'], 0, ',', '.') ?></td>
            </tr>
          <?php endforeach; ?>
          <tr class="bold">
            <td colspan="2" class="right">Total <?= Html::encode($section) ?></td>
            <td class="right"><?= number_format($row['total'], 0, ',', '.') ?></td>
          </tr>
        <?php endforeach; ?>

        <tr class="bold" style="background:#fafafa">
          <td colspan="2" class="right">LABA KOTOR</td>
          <td class="right"><?= number_format($data['Laba Kotor'] ?? 0, 0, ',', '.') ?></td>
        </tr>
        <tr class="bold" style="background:#eaeaea">
          <td colspan="2" class="right">LABA BERSIH</td>
          <td class="right"><?= number_format($data['Laba Bersih'] ?? 0, 0, ',', '.') ?></td>
        </tr>
      </tbody>
    </table>
<?php
    $html = ob_get_clean(); // Ambil hasil output buffering

    $mpdf->WriteHTML($html);
    return $mpdf->Output("Laba_Rugi_{$tahun}_{$bulan_dari}_to_{$bulan_hingga}.pdf", \Mpdf\Output\Destination::DOWNLOAD);
  }

  private function exportExcelLabaRugi(array $data, int $tahun, int $bulan_dari = 1, int $bulan_hingga = 12)
  {
    $sheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $xls = $sheet->getActiveSheet();

    $namaBulan = function ($bulan) {
      return ucfirst(\DateTime::createFromFormat('!m', $bulan)->format('F'));
    };

    $periodeText = "{$namaBulan($bulan_dari)} - {$namaBulan($bulan_hingga)} {$tahun}";
    $xls->setTitle("Laba Rugi {$tahun}");

    $xls->setCellValue('A1', 'Laporan Laba Rugi');
    $xls->setCellValue('A2', "Periode: {$periodeText}");
    $xls->mergeCells('A1:C1');
    $xls->mergeCells('A2:C2');
    $xls->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    $xls->getStyle('A2')->getFont()->setItalic(true);

    $xls->fromArray(['Kode', 'Keterangan', 'Jumlah (Rp)'], null, 'A3');
    $row = 4;

    foreach ($data as $section => $rowData) {
      if (!is_array($rowData) || !isset($rowData['detail'])) continue;

      $xls->setCellValue("A{$row}", $section);
      $xls->getStyle("A{$row}:C{$row}")->getFont()->setBold(true);
      $row++;

      foreach ($rowData['detail'] as $d) {
        $xls->setCellValue("A{$row}", $d['kode'])
          ->setCellValue("B{$row}", $d['nama'])
          ->setCellValue("C{$row}", $d['nom']);
        $row++;
      }

      $xls->setCellValue("B{$row}", "Total {$section}")
        ->setCellValue("C{$row}", $rowData['total']);
      $xls->getStyle("A{$row}:C{$row}")->getFont()->setBold(true);
      $row++;
    }

    $labaKotor = $data['Laba Kotor'] ?? 0;
    $labaBersih = $data['Laba Bersih'] ?? 0;

    $xls->setCellValue("B{$row}", "LABA KOTOR")
      ->setCellValue("C{$row}", $labaKotor);
    $xls->getStyle("A{$row}:C{$row}")->getFont()->setBold(true);
    $row++;

    $xls->setCellValue("B{$row}", "LABA BERSIH")
      ->setCellValue("C{$row}", $labaBersih);
    $xls->getStyle("A{$row}:C{$row}")->getFont()->setBold(true);

    $last = $row;
    $xls->getStyle("C4:C{$last}")
      ->getNumberFormat()->setFormatCode('#,##0');
    foreach (['A', 'B', 'C'] as $col)
      $xls->getColumnDimension($col)->setAutoSize(true);

    $tmp = tmpfile();
    $path = stream_get_meta_data($tmp)['uri'];
    (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($sheet))->save($path);

    return Yii::$app->response->sendFile(
      $path,
      "Laba_Rugi_{$tahun}_{$bulan_dari}_to_{$bulan_hingga}.xlsx",
      ['mimeType' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
    );
  }
}
