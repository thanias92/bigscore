<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Laporan Pengeluaran Bulanan';
$this->params['breadcrumbs'][] = $this->title;

// Tahun dari 2020 sampai sekarang
$yearList = range(date('Y'), 2020);

$this->registerCss(<<<CSS
.accordion-toggle.active-row {
    background-color: #f2dede !important;
    transition: background-color 0.3s ease;
}
.toggle-icon {
    font-weight: bold;
    color: #c0392b;
}
.select.form-control {
    min-width: 120px;
}
.badge-success {
    background-color: #2ecc71;
    color: #fff;
    padding: 3px 6px;
    font-size: 12px;
    border-radius: 4px;
}
.table-sm th, .table-sm td {
    font-size: 12px;
}
CSS);
$bulanList = [
    '01' => 'Januari',
    '02' => 'Februari',
    '03' => 'Maret',
    '04' => 'April',
    '05' => 'Mei',
    '06' => 'Juni',
    '07' => 'Juli',
    '08' => 'Agustus',
    '09' => 'September',
    '10' => 'Oktober',
    '11' => 'November',
    '12' => 'Desember'
];

// Tahun dari 5 tahun lalu hingga tahun depan
$yearList = range(date('Y') + 1, date('Y') - 5);
?>

<div class="laporan-pengeluaran">
    <div class="p-4 bg-white shadow rounded">
        <h1><?= Html::encode($this->title) ?></h1>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0 text-dark">Filter Laporan Pemasukan</h6>
            </div>
            <div class="card-body">
                <?php $form = ActiveForm::begin([
                    'method' => 'get',
                    'action' => ['pengeluaran'],
                    'options' => ['class' => 'form-row align-items-end']
                ]); ?>

                <div class="form-group col-md-6">
                    <label>Pilih Tahun</label>
                    <div class="input-group">
                        <select name="tahun_awal" class="form-control custom-select">
                            <?php foreach ($yearList as $y): ?>
                                <option value="<?= $y ?>" <?= $tahun_awal == $y ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="input-group-append">
                            <span class="input-group-text bg-white border-left-0">hingga</span>
                        </div>
                        <select name="tahun_akhir" class="form-control custom-select">
                            <?php foreach ($yearList as $y): ?>
                                <option value="<?= $y ?>" <?= $tahun_akhir == $y ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group col-md-6">
                    <label>Pilih Bulan</label>
                    <div class="input-group">
                        <select name="bulan_awal" class="form-control custom-select">
                            <?php foreach ($bulanList as $key => $val): ?>
                                <option value="<?= $key ?>" <?= $bulan_awal == $key ? 'selected' : '' ?>><?= $val ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="input-group-append">
                            <span class="input-group-text bg-white border-left-0">hingga</span>
                        </div>
                        <select name="bulan_akhir" class="form-control custom-select">
                            <?php foreach ($bulanList as $key => $val): ?>
                                <option value="<?= $key ?>" <?= $bulan_akhir == $key ? 'selected' : '' ?>><?= $val ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group col-12 mt-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Tampilkan
                    </button>
                    <div class="btn-group ml-2" role="group">
                        <a href="<?= Url::to([
                                        'pemasukan',
                                        'tahun_awal' => $tahun_awal,
                                        'bulan_awal' => $bulan_awal,
                                        'tahun_akhir' => $tahun_akhir,
                                        'bulan_akhir' => $bulan_akhir,
                                        'export' => true
                                    ]) ?>" class="btn btn-success">
                            <i class="fas fa-file-excel"></i> Ekspor Excel
                        </a>
                    </div>
                    <div class="btn-group ml-2" role="group">
                        <a href="<?= Url::to([
                                        'pemasukan',
                                        'tahun_awal' => $tahun_awal,
                                        'bulan_awal' => $bulan_awal,
                                        'tahun_akhir' => $tahun_akhir,
                                        'bulan_akhir' => $bulan_akhir,
                                        'export' => 'pdf'
                                    ]) ?>" class="btn btn-danger">
                            <i class="fas fa-file-pdf"></i> Ekspor PDF
                        </a>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>
            </div>

            <table class="table table-bordered table-hover">
                <thead style="background-color:#c0392b; color:white;">
                    <tr>
                        <th style="width: 40px;"></th>
                        <th>Bulan</th>
                        <th>Total Pengeluaran</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $grandTotal = 0; ?>
                    <?php foreach ($data as $index => $row): ?>
                        <?php $grandTotal += $row['total_pengeluaran']; ?>
                        <tr class="accordion-toggle" data-target="#detail-<?= $index ?>" data-bulan="<?= $row['bulan'] ?>" style="cursor:pointer;">
                            <td class="text-center"><span class="toggle-icon">▼</span></td>
                            <td><?= Yii::$app->formatter->asDate($row['bulan'] . '-01', 'MMMM yyyy') ?></td>
                            <td><?= 'Rp. ' . number_format($row['total_pengeluaran'], 0, ',', '.') ?></td>
                        </tr>
                        <tr id="detail-<?= $index ?>" class="detail-row" style="display: none;">
                            <td colspan="3" id="detail-content-<?= $index ?>">
                                <div class="text-center text-muted">Memuat data...</div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr style="font-weight:bold; background:#f1f1f1;">
                        <td colspan="2">Total Keseluruhan</td>
                        <td><?= 'Rp. ' . number_format($grandTotal, 0, ',', '.') ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <?php
    $urlDetail = Url::to(['/vendorfinance/laporan/ajax-detail-pengeluaran']);
    $js = <<<JS
$('.accordion-toggle').on('click', function() {
    var target = $(this).data('target');
    var \$detailRow = $(target);
    var \$content = \$detailRow.find('td');
    var \$icon = $(this).find('.toggle-icon');

    if (\$detailRow.is(':visible')) {
        \$detailRow.hide();
        $(this).removeClass('active-row');
        \$icon.text('▼');
    } else {
        \$detailRow.show();
        $(this).addClass('active-row');
        \$icon.text('▲');
        if (\$content.text().trim() === 'Memuat data...') {
            var bulan = $(this).data('bulan');
            $.get('$urlDetail', { bulan: bulan }, function (html) {
                \$content.html(html);
            });
        }
    }
});
JS;
    $this->registerJs($js);
    ?>