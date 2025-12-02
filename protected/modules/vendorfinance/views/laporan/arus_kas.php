<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Laporan Arus Kas Tahunan';
$this->params['breadcrumbs'][] = $this->title;

$this->registerCss(<<<CSS
.accordion-row.active,
.accordion-row-bulan.active {
    background: #eef2ff;
}
.toggle-icon,
.toggle-icon-bulan {
    font-weight: bold;
    color: #34495e;
}
CSS);
?>
<div class="p-4 bg-white shadow rounded">
<h1><?= Html::encode($this->title) ?></h1>

<div class="mb-3">
  <button class="btn btn-success btn-export" data-export-type="excel">Ekspor Excel</button>
  <button class="btn btn-primary btn-export" data-export-type="pdf">Ekspor PDF</button>
</div>

<table class="table table-bordered table-hover">
    <thead class="thead-dark">
        <tr>
            <th style="width:40px;"></th>
            <th>Tahun</th>
            <th>Pemasukan</th>
            <th>Pengeluaran</th>
            <th>Saldo</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data as $row): ?>
            <?php $tahun = $row['tahun']; ?>
            <tr class="accordion-row"
                data-target="#detail-<?= $tahun ?>"
                data-tahun="<?= $tahun ?>"
                style="cursor:pointer;">
                <td class="text-center"><span class="toggle-icon">▼</span></td>
                <td><?= $tahun ?></td>
                <td><?= 'Rp ' . number_format($row['masuk'], 0, ',', '.') ?></td>
                <td><?= 'Rp ' . number_format($row['keluar'], 0, ',', '.') ?></td>
                <td><?= 'Rp ' . number_format($row['saldo'], 0, ',', '.') ?></td>
            </tr>
            <tr id="detail-<?= $tahun ?>" class="detail-row" style="display:none;" data-tahun="<?= $tahun ?>">
                <td colspan="5" class="detail-cell text-center text-muted">
                    Memuat data...
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<br>
</div>
<?php
$urlDetailTahun  = Url::to(['/vendorfinance/laporan/ajax-detail-arus-kas']);
$urlDetailHarian = Url::to(['/vendorfinance/laporan/ajax-detail-harian']);
$exportUrl = Url::to(['arus-kas']); // base url

$js = <<<JS
$('body').on('click', '.accordion-row', function () {
    const target   = $(this).data('target');
    const tahun    = $(this).data('tahun');
    const \$detail = $(target);
    const \$cell   = \$detail.find('.detail-cell');
    const \$icon   = $(this).find('.toggle-icon');

    if (\$detail.is(':visible')) {
        \$detail.hide();
        $(this).removeClass('active');
        \$icon.text('▼');
        return;
    }

    $('.detail-row').hide();
    $('.accordion-row').removeClass('active').find('.toggle-icon').text('▼');

    \$detail.show();
    $(this).addClass('active');
    \$icon.text('▲');

    \$cell.text('Memuat data...');
    $.get('$urlDetailTahun', { tahun: tahun }, function (html) {
        \$cell.html(html);
    });
});

$('body').on('click', '.accordion-row-bulan', function() {
    const target  = $(this).data('target');
    const tahun   = $(this).data('tahun');
    const bulan   = $(this).data('bulan');
    const \$detail = $(target);
    const \$icon   = $(this).find('.toggle-icon-bulan');

    if (\$detail.is(':visible')) {
        \$detail.hide();
        $(this).removeClass('active');
        \$icon.text('▼');
        return;
    }

    $('.detail-row-bulan').hide();
    $('.accordion-row-bulan').removeClass('active').find('.toggle-icon-bulan').text('▼');

    \$detail.show();
    $(this).addClass('active');
    \$icon.text('▲');

    \$detail.find('.detail-cell-bulan').html('Memuat data...');
    $.get('$urlDetailHarian', { tahun: tahun, bulan: bulan }, function(html) {
        \$detail.find('.detail-cell-bulan').html(html);
    });
});

$('.btn-export').on('click', function () {
  const type = $(this).data('export-type');
  const tahunSekarang = new Date().getFullYear();

  const bulanOptions = Array.from({ length: 12 }, function(_, i) {
    const bulanNum = i + 1;
    const bulanNama = new Date(0, i).toLocaleString('id-ID', { month: 'long' });
    return '<option value="' + bulanNum + '">' + bulanNum + ' - ' + bulanNama + '</option>';
  }).join('');

  Swal.fire({
    title: 'Pilih Tahun dan Rentang Bulan',
    html:
      '<input id="swal-input1" type="number" class="swal2-input" placeholder="Tahun" value="' + tahunSekarang + '">' +
      '<select id="swal-input2" class="swal2-input">' + bulanOptions + '</select>' +
      '<select id="swal-input3" class="swal2-input">' + bulanOptions + '</select>',
    focusConfirm: false,
    showCancelButton: true,
    confirmButtonText: 'Ekspor',
    preConfirm: () => {
      const tahun = document.getElementById('swal-input1').value;
      const dari = document.getElementById('swal-input2').value;
      const hingga = document.getElementById('swal-input3').value;

      if (!tahun || parseInt(dari) > parseInt(hingga)) {
        Swal.showValidationMessage('Pastikan input valid dan "dari bulan" ≤ "hingga bulan"');
        return false;
      }
      return { tahun, dari, hingga };
    }
  }).then((res) => {
    if (res.isConfirmed) {
      const { tahun, dari, hingga } = res.value;
      window.open('$exportUrl?export=' + type + '-detail&tahun=' + tahun + '&bulan_dari=' + dari + '&bulan_hingga=' + hingga, '_blank');
    }
  });
});
JS;
$this->registerJs($js);
?>
