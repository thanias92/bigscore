<?php
use yii\helpers\Html;

// Kelompokkan pemasukan berdasarkan parent_id
$grouped = [];
foreach ($pemasukan as $item) {
  $parentId = $item->parent_id ?: $item->pemasukan_id; // gunakan dirinya sendiri jika bukan anak
  $grouped[$parentId][] = $item;
}
?>

<?php if (empty($grouped)): ?>
  <div class="text-muted text-center">Tidak ada data pemasukan</div>
<?php else: ?>
  <?php foreach ($grouped as $items): ?>
    <div class="card mb-3 ">
      <div class="card-header bg-primary text-white"style="width: 15%; padding: 0.4rem 0.6rem; ">
        <?= Html::encode($items[0]->invoice_number ?? 'No Invoice') ?>
        — <?= Html::encode($items[0]->deals->customer->customer_name ?? '-') ?>
      </div>
      <div class="card-body p-2">
        <table class="table table-sm table-bordered mb-0">
          <thead class="thead-light">
            <tr>
              <th style="width: 15%">Tanggal</th>
              <th style="width: 20%">No Faktur</th>
              <th style="width: 15%">Total</th>
              <th style="width: 15%">Status</th>
              <th>Deskripsi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($items as $entry): ?>
              <tr>
                <td><?= Yii::$app->formatter->asDate($entry->purchase_date) ?></td>
                <td>
                  <?= Html::encode($entry->no_faktur) ?>
                  <?php if ($entry->parent_id): ?>
                    <small class="text-muted">(Cicilan)</small>
                  <?php endif; ?>
                </td>
                <td>Rp<?= number_format($entry->grand_total, 0, ',', '.') ?></td>
                <td><?= Html::encode($entry->status ?? '-') ?></td>
                <td><?= Html::encode($entry->description ?? '-') ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php endforeach; ?>
<?php endif; ?>
