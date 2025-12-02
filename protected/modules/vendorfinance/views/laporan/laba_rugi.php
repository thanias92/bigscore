<?php

use yii\helpers\Html;
use yii\helpers\Url;

$tahun = $tahun ?? date('Y');

$this->title = "Laporan Laba Rugi $tahun";
$this->params['breadcrumbs'][] = $this->title;

$bulanList = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];
?>

<div class="laporan-laba-rugi">
<div class="p-4 bg-white shadow rounded">
    <h3><?= Html::encode($this->title) ?></h3>

    <p>
        <button class="btn btn-danger btn-export-pdf">Export PDF</button>
        <button class="btn btn-success btn-export-excel">Export Excel</button>
    </p>

    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Keterangan</th>
                <th class="text-right">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $pengeluaranSections = ['Beban Pokok Pendapatan', 'Beban Operasional', 'Beban Lainnya'];
            foreach ($data as $section => $row):
                if (!is_array($row) || !isset($row['detail'])) continue;
                if (in_array($section, ['Laba Kotor', 'Laba Bersih'])) continue;

                $isPengeluaran = in_array($section, $pengeluaranSections);
                $sectionStyle = $isPengeluaran
                    ? 'style="background-color:#fce4e4;font-weight:bold;"'
                    : 'class="table-primary font-weight-bold"';
            ?>
                <tr <?= $sectionStyle ?>>
                    <td colspan="3"><?= Html::encode($section) ?></td>
                </tr>

                <?php foreach ($row['detail'] as $d): ?>
                    <tr <?= ($d['nom'] < 0 ? 'style="background-color:#ffecec;"' : '') ?>>
                        <td><?= Html::encode($d['kode']) ?></td>
                        <td><?= Html::encode($d['nama']) ?></td>
                        <td class="text-right"><?= 'Rp ' . number_format($d['nom'], 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>

                <tr class="font-weight-bold">
                    <td colspan="2" class="text-right">Total <?= Html::encode($section) ?></td>
                    <td class="text-right"><?= 'Rp ' . number_format($row['total'], 0, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>

            <tr class="table-warning font-weight-bold">
                <td colspan="2" class="text-right">LABA KOTOR</td>
                <td class="text-right"><?= 'Rp ' . number_format($data['Laba Kotor'] ?? 0, 0, ',', '.') ?></td>
            </tr>
            <tr class="table-success font-weight-bold">
                <td colspan="2" class="text-right">LABA BERSIH</td>
                <td class="text-right"><?= 'Rp ' . number_format($data['Laba Bersih'] ?? 0, 0, ',', '.') ?></td>
            </tr>
        </tbody>
    </table>
</div>
</div>

<?php
$this->registerJsFile('https://cdn.jsdelivr.net/npm/sweetalert2@11', ['depends' => [\yii\web\JqueryAsset::class]]);

// Dropdown tahun dan bulan
$years = range(date('Y'), 2020);
$yearOptions = '';
foreach ($years as $y) {
    $selected = ($y == $tahun) ? 'selected' : '';
    $yearOptions .= "<option value='{$y}' {$selected}>{$y}</option>";
}
$bulanOptions = '';
foreach ($bulanList as $val => $label) {
    $bulanOptions .= "<option value='{$val}'>{$label}</option>";
}

$exportUrl = Url::to(['laba-rugi']);

$js = <<<JS
function showExportDialog(type) {
  const htmlForm = `
    <div>
      <label class="swal2-label">Tahun</label>
      <select id="swal-tahun" class="swal2-input">{$yearOptions}</select>
      <label class="swal2-label">Dari Bulan</label>
      <select id="swal-dari" class="swal2-input">{$bulanOptions}</select>
      <label class="swal2-label">Hingga Bulan</label>
      <select id="swal-hingga" class="swal2-input">{$bulanOptions}</select>
    </div>`;

  Swal.fire({
    title: 'Pilih Tahun dan Rentang Bulan',
    html: htmlForm,
    width: 500,
    focusConfirm: false,
    confirmButtonText: 'Ekspor',
    cancelButtonText: 'Batal',
    showCancelButton: true,
    preConfirm: () => {
      const tahun = document.getElementById('swal-tahun').value;
      const dari = document.getElementById('swal-dari').value;
      const hingga = document.getElementById('swal-hingga').value;

      if (parseInt(dari) > parseInt(hingga)) {
        Swal.showValidationMessage('Bulan awal tidak boleh lebih besar dari bulan akhir.');
        return false;
      }

      return { tahun, dari, hingga };
    }
  }).then((res) => {
    if (res.isConfirmed) {
      const { tahun, dari, hingga } = res.value;
      const url = '$exportUrl?export=' + type + '&tahun=' + tahun + '&bulan_dari=' + dari + '&bulan_hingga=' + hingga;
      window.open(url, '_blank');
    }
  });
}

document.querySelector('.btn-export-pdf').addEventListener('click', function(e) {
  e.preventDefault();
  showExportDialog('pdf');
});
document.querySelector('.btn-export-excel').addEventListener('click', function(e) {
  e.preventDefault();
  showExportDialog('excel');
});
JS;
$this->registerJs($js);

$this->registerCss("
  .swal2-label {
    display: block;
    margin: 10px 0 5px;
    font-weight: bold;
    text-align: left;
  }
  .swal2-input {
    width: 100% !important;
  }
");
?>
