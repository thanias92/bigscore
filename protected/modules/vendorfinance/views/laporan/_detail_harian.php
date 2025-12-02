<?php if (empty($data)): ?>
  <em>Tidak ada transaksi harian.</em>
<?php else: ?>
  <table class="table table-sm table-bordered">
    <thead class="table-light">
      <tr>
        <th style="width:160px;">Tanggal</th>
        <th>Pemasukan</th>
        <th>Pengeluaran</th>
        <th>Saldo</th>
      </tr>
    </thead>
    <tbody>
      <?php
        $totalMasuk = 0;
        $totalKeluar = 0;
      ?>
      <?php foreach ($data as $tgl => $row): ?>
        <?php
          $saldo = $row['masuk'] - $row['keluar'];
          $totalMasuk += $row['masuk'];
          $totalKeluar += $row['keluar'];
        ?>
        <tr>
          <td><?= Yii::$app->formatter->asDate($tgl, 'dd MMMM yyyy') ?></td>
          <td><?= 'Rp ' . number_format($row['masuk'], 0, ',', '.') ?></td>
          <td><?= 'Rp ' . number_format($row['keluar'], 0, ',', '.') ?></td>
          <td><?= 'Rp ' . number_format($saldo, 0, ',', '.') ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
    <tfoot class="table-light fw-bold">
      <tr>
        <td class="text-center">Total</td>
        <td><?= 'Rp ' . number_format($totalMasuk, 0, ',', '.') ?></td>
        <td><?= 'Rp ' . number_format($totalKeluar, 0, ',', '.') ?></td>
        <td><?= 'Rp ' . number_format($totalMasuk - $totalKeluar, 0, ',', '.') ?></td>
      </tr>
    </tfoot>
  </table>
<?php endif; ?>
