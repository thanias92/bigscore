<?php
// app/modules/vendorfinance/views/pemasukan/_pemasukan_details.php
use yii\helpers\Html;
use yii\helpers\Url;
use Yii;

/* @var $this yii\web\View */
/* @var $pemasukan app\models\Pemasukan */
/* @var $cicilanList app\models\PemasukanCicilan[] */
?>

<div class="modal-header">
    <h5 class="modal-title" id="modalLabel">Detail Tagihan: <?= Html::encode($pemasukan->no_faktur) ?></h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-6">
            <p><strong>Customer:</strong> <?= Html::encode($pemasukan->deals->customer->customer_name ?? '-') ?></p>
            <p><strong>Produk:</strong> <?= Html::encode($pemasukan->produk) ?></p>
            <p><strong>Tipe Pembelian:</strong> <?= Html::encode($pemasukan->purchase_type) ?></p>
        </div>
        <div class="col-md-6">
            <p><strong>Total Tagihan:</strong> <?= Yii::$app->formatter->asCurrency($pemasukan->grand_total, 'IDR') ?></p>
            <p><strong>Sisa Tagihan:</strong> <span id="pemasukan-sisa-tagihan"><?= Yii::$app->formatter->asCurrency($pemasukan->sisa_tagihan, 'IDR') ?></span></p>
            <p><strong>Status:</strong> <span id="pemasukan-status"><?= Html::encode($pemasukan->status) ?></span></p>
        </div>
    </div>

    <?php if ($pemasukan->purchase_type === 'outrightPurchaseI' && $pemasukan->cicilan > 0): ?>
        <hr>
        <h6>Daftar Cicilan (<?= Html::encode($pemasukan->cicilan) ?> kali)</h6>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Ke</th>
                        <th>Nominal</th>
                        <th>Jatuh Tempo</th>
                        <th>Status</th>
                        <th>Tanggal Bayar</th>
                        <th>Bukti</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($cicilanList)): ?>
                        <tr><td colspan="7" class="text-center">Belum ada cicilan yang dibuat.</td></tr>
                    <?php else: ?>
                        <?php foreach ($cicilanList as $cicilan): ?>
                            <tr id="cicilan-row-<?= $cicilan->id ?>">
                                <td><?= Html::encode($cicilan->ke) ?></td>
                                <td><?= Yii::$app->formatter->asCurrency($cicilan->nominal, 'IDR') ?></td>
                                <td><?= Yii::$app->formatter->asDate($cicilan->jatuh_tempo, 'long') ?></td>
                                <td><span class="badge bg-<?= ($cicilan->status === 'Lunas' ? 'success' : ($cicilan->status === 'Menunggu' && strtotime($cicilan->jatuh_tempo) < time() ? 'danger' : 'warning')) ?>"><?= Html::encode($cicilan->status) ?></span></td>
                                <td><?= $cicilan->tanggal_bayar ? Yii::$app->formatter->asDate($cicilan->tanggal_bayar, 'long') : '-' ?></td>
                                <td>
                                    <?php if ($cicilan->bukti_path): ?>
                                        <?= Html::a('Lihat Bukti', Url::to(['get-bukti', 'filename' => basename($cicilan->bukti_path)]), ['target' => '_blank', 'class' => 'btn btn-sm btn-info']) ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($cicilan->status === 'Menunggu'): ?>
                                        <?= Html::button('Bayar', [
                                            'class' => 'btn btn-sm btn-primary bayar-cicilan-btn',
                                            'data-pemasukan-id' => $pemasukan->pemasukan_id,
                                            'data-cicilan-id' => $cicilan->id, // Pass cicilan_id
                                            'data-cicilan-nominal' => $cicilan->nominal, // Pass nominal
                                            'data-cicilan-ke' => $cicilan->ke, // Pass installment number
                                        ]) ?>
                                    <?php else: ?>
                                        Lunas
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <hr>
        <p>Invoice ini adalah pembayaran penuh (non-cicilan).</p>
        <?php if ($pemasukan->status !== 'Lunas'): ?>
            <?= Html::button('Lakukan Pembayaran', [
                'class' => 'btn btn-success trigger-penerimaan-pembayaran',
                'data-id' => $pemasukan->pemasukan_id,
            ]) ?>
        <?php endif; ?>
    <?php endif; ?>
</div>

<div class="modal-footer">
    <?= Html::button('Tutup', ['class' => 'btn btn-secondary', 'data-bs-dismiss' => 'modal']) ?>
</div>

<?php
$js = <<<JS
$(document).ready(function() {
    // Handler for "Bayar Cicilan" button
    $('.bayar-cicilan-btn').on('click', function() {
        var pemasukanId = $(this).data('pemasukan-id');
        var cicilanId = $(this).data('cicilan-id');
        var cicilanNominal = $(this).data('cicilan-nominal');
        var cicilanKe = $(this).data('cicilan-ke');

        // Open the Penerimaan Pembayaran modal
        $.ajax({
            url: 'penerimaan-pembayaran?id=' + pemasukanId,
            type: 'GET',
            success: function(response) {
                $('#modal').find('.modal-content').html(response);
                $('#modal').modal('show');

                // Pre-fill the relevant fields for installment payment
                $('#jumlah-pembayaran-input').val(cicilanNominal);
                $('#jumlah-pembayaran-input').prop('readonly', true); // Make it read-only
                $('#penerimaanpembayaran-catatan').val('Pembayaran Cicilan Ke-' + cicilanKe + ' untuk Faktur: ' + $('#pemasukan-no_faktur').val());
                
                // Hide fields not relevant for typical installment payments
                $('#potongan-pajak-bank-input').closest('.form-group').hide();
                $('#diskon-type-dropdown').closest('.row').hide();
                $('#diskon-rp-row').hide();
                
            },
            error: function(xhr) {
                Swal.fire('Error!', 'Gagal memuat form pembayaran: ' + xhr.responseText, 'error');
            }
        });
    });

    // Handler for "Lakukan Pembayaran" button for non-installment (full) invoices
    $('.trigger-penerimaan-pembayaran').on('click', function() {
        var pemasukanId = $(this).data('id');
        $.ajax({
            url: 'penerimaan-pembayaran?id=' + pemasukanId,
            type: 'GET',
            success: function(response) {
                $('#modal').find('.modal-content').html(response);
                $('#modal').modal('show');
                // For full payment, no pre-fill, allow full entry.
                // The _penerimaan_pembayaran.php JS will handle default visibility and calculations.
                // You might want to pre-fill 'jumlah-pembayaran-input' with 'sisa_tagihan' here
                // if it's a direct payment to encourage full settlement.
                // Example: $('#jumlah-pembayaran-input').val(parseFloat($('#grand-total-display').val().replace(/[^0-9,-]+/g, "").replace(",", ".")) - (parseFloat($('#penerimaanpembayaran-potongan_pajak_bank').val()) || 0) - (parseFloat($('#diskon-value-input').val()) || 0));
                // Or simply leave it empty for user input.
            },
            error: function(xhr) {
                Swal.fire('Error!', 'Gagal memuat form pembayaran: ' + xhr.responseText, 'error');
            }
        });
    });
});
JS;
$this->registerJs($js);
?>