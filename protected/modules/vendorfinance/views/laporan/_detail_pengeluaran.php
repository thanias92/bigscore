<?php

use yii\helpers\Html;

/** @var \app\models\Pengeluaran[] $pengeluaran */
if (empty($pengeluaran)) {
    echo "<p class='text-center text-muted'>Tidak ada data pengeluaran bulan ini.</p>";
    return;
}
?>

<table class="table table-bordered table-sm">
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>No Pengeluaran</th>
            <th>Kategori</th>
            <th>Deskripsi</th>
            <th>Vendor</th>
            <th>Status</th>
            <th class="text-end">Jumlah</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($pengeluaran as $item): ?>
            <tr>
                <td><?= Yii::$app->formatter->asDate($item->tanggal) ?></td>
                <td><?= Html::encode($item->no_pengeluaran) ?></td>
                <td><?= $item->accountkeluar->akun ?? '-' ?></td>
                <td><?= Html::encode($item->keterangan) ?></td>
                <td><?= $item->vendor->nama_vendor ?? '-' ?></td>
                <td><?= $item->status_pembayaran ?? '-' ?></td>
                <td><?= 'Rp ' . number_format($item->jumlah, 0, ',', '.') ?></td>            </tr>
        <?php endforeach; ?>
    </tbody>
</table>