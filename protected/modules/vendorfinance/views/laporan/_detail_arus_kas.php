<?php use yii\helpers\Html; ?>

<?php if (empty($bulanList)): ?>
  <em>Tidak ada data.</em>
<?php else: ?>
  <table class="table table-bordered table-sm mb-0">
    <thead class="table-secondary">
      <tr>
        <th style="width: 40px;"></th>
        <th>Bulan</th>
        <th>Pemasukan</th>
        <th>Pengeluaran</th>
        <th>Saldo</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($bulanList as $bulan): ?>
        <?php
          $tahun = $bulan['tahun'];
          $bln   = $bulan['bulan'];
          $idRow = "detail-bulan-{$tahun}-{$bln}";
        ?>
        <tr class="accordion-row-bulan"
            data-target="#<?= $idRow ?>"
            data-tahun="<?= $tahun ?>"
            data-bulan="<?= $bln ?>"
            style="cursor:pointer;">
          <td class="text-center"><span class="toggle-icon-bulan">▼</span></td>
          <td><?= Html::encode($bulan['label']) ?></td>
          <td><?= 'Rp ' . number_format($bulan['masuk'], 0, ',', '.') ?></td>
          <td><?= 'Rp ' . number_format($bulan['keluar'], 0, ',', '.') ?></td>
          <td><?= 'Rp ' . number_format($bulan['saldo'], 0, ',', '.') ?></td>
        </tr>
        <tr id="<?= $idRow ?>" class="detail-row-bulan" style="display: none;">
          <td colspan="5" class="detail-cell-bulan text-center text-muted">
            Memuat data...
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>
