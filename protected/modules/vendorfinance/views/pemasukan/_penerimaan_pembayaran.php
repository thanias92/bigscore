<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\widgets\JSRegister;
use kartik\date\DatePicker;
/* @var $this yii\web\View */
/* @var $model app\models\PenerimaanPembayaran */
/* @var $pemasukan app\models\Pemasukan */
/* @var $akunPemasukanList app\models\Accountkeluar[] */
/* @var $cicilanAktif app\models\PemasukanCicilan|null */

?>

<?php if ($pemasukan->status === 'Lunas'): ?>
    <div class="alert alert-success">
        <strong>Pembayaran Lunas:</strong> Semua tagihan telah dibayar. Form pembayaran tidak tersedia.
    </div>
<?php else: ?>

    <div class="penerimaan-pembayaran-form">
        <?php $form = ActiveForm::begin([
            'id' => 'penerimaan-pembayaran-form',
            'action' => ['process-penerimaan-pembayaran', 'pemasukan_id' => $pemasukan->pemasukan_id],
            'options' => ['enctype' => 'multipart/form-data'],
        ]); ?>

        <?php if (!empty($model->pemasukan_cicilan_id)): ?>
            <?= Html::activeHiddenInput($model, 'pemasukan_cicilan_id') ?>
        <?php endif; ?>

        <?php if (!empty($cicilanAktif)): ?>
            <div class="alert alert-info">
                <strong>Pembayaran Cicilan ke-<?= Html::encode($cicilanAktif->ke) ?></strong>
            </div>
        <?php endif; ?>

        <div class="mb-3">
            <strong>No Faktur:</strong> <?= Html::encode($pemasukan->no_faktur) ?><br>
            <strong>Total Tagihan:</strong> <?= 'Rp' . number_format($pemasukan->grand_total, 0, ',', '.') ?><br>
            <strong>Jumlah Terbayar:</strong> <?= 'Rp' . number_format($pemasukan->getJumlahTerbayar(), 0, ',', '.') ?><br>
            <strong>Sisa Tagihan:</strong> <span id="sisa-tagihan"><?= 'Rp' . number_format($pemasukan->getSisaTagihan(), 0, ',', '.') ?></span>

        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'tanggal_bukti_transfer')->widget(DatePicker::class, [
                    'options' => ['autocomplete' => 'off', 'id' => 'tanggal-bukti-transfer'],
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'dd/mm/yyyy',
                        'todayHighlight' => true,
                    ],
                ])->label('Tanggal Pembayaran <span class="text-danger">*</span>', ['encode' => false]) ?>
            </div>
            <div class="col-md-6">
                <label for="jumlah-terbayar">Jumlah Terbayar</label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <?= Html::textInput('jumlah_terbayar_display', number_format($pemasukan->getSisaTagihan(), 0, ',', '.'), [
                        'class' => 'form-control',
                        'readonly' => true,
                        'id' => 'jumlah-terbayar-display'
                    ]) ?>
                </div>
                <?= Html::activeHiddenInput($model, 'jumlah_terbayar', [
                    'value' => (int)$pemasukan->getSisaTagihan(),
                    'id' => 'jumlah-terbayar'
                ]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'potongan_pajak')->input('number', ['min' => 0, 'step' => 'any']) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'tipe_pembayaran')->dropDownList(
                    ['transfer' => 'Transfer', 'cash' => 'Cash'],
                    ['prompt' => '-- Pilih Tipe Pembayaran --']
                )->label('Tipe Pembayaran <span class="text-danger">*</span>', ['encode' => false]) ?>
            </div>

            <div class="col-md-12">
                <?= $form->field($model, 'accountkeluar_id')->widget(\kartik\select2\Select2::class, [
                    'data' => ArrayHelper::map($akunPemasukanList, 'id', function ($a) {
                        return "[{$a->code}] - {$a->akun}";
                    }),
                    'options' => [
                        'placeholder' => '-- Pilih Akun Pemasukan --',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'theme' => \kartik\select2\Select2::THEME_KRAJEE_BS5,
                        'dropdownParent' => new \yii\web\JsExpression("$('#modal-terima-pembayaran')"),
                    ],
                ]) ?>
            </div>

            <div class="col-md-12">
                <?= $form->field($model, 'deskripsi')->textarea(['rows' => 3]) ?>
            </div>

            <div class="col-md-12">
                <?= $form->field($model, 'bukti_transfer')->fileInput([
                    'accept' => '.jpg,.jpeg,.png,.pdf'
                ])->label('Upload Bukti Pembayaran <span class="text-danger">*</span>', ['encode' => false]) ?>
            </div>

            <?php if ($model->bukti_transfer): ?>
                <div class="col-md-12 mt-2">
                    <strong>Preview Bukti:</strong><br>
                    <?php
                    $ext = pathinfo($model->bukti_transfer, PATHINFO_EXTENSION);
                    $url = Url::to(['/vendorfinance/pemasukan/bukti', 'file' => $model->bukti_transfer]);
                    if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png'])) {
                        echo Html::img($url, ['style' => 'max-width:100%; max-height:300px']);
                    } else {
                        echo Html::a('Lihat Bukti PDF', $url, ['target' => '_blank']);
                    }
                    ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="form-group mt-3">
            <?= Html::submitButton('Simpan Pembayaran', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

    <?php
    $this->registerJs(<<<JS
    $('#penerimaan-pembayaran-form').on('beforeSubmit', function(e) {
        e.preventDefault();
        var form = $(this);

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: new FormData(this),
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sukses',
                        text: response.message,
                    }).then(() => {
                        $('#modal').modal('hide');
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.message,
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Server',
                    text: 'Terjadi kesalahan saat mengirim data.',
                });
            }
        });

        return false;
    });
    JS);
    ?>

<?php endif; ?>

<?php if ($pemasukan->penerimaanPembayarans && count($pemasukan->penerimaanPembayarans) > 0): ?>
    <div class="mt-4">
        <h5>Riwayat Pembayaran</h5>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Tanggal Pembayaran</th>
                    <th>Jumlah Dibayar</th>
                    <th>Pajak</th>
                    <th>Metode</th>
                    <th>Akun</th>
                    <th>Cicilan Ke</th>
                    <th>Bukti</th>
                    <th>Deskripsi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pemasukan->penerimaanPembayarans as $pembayaran): ?>
                    <tr>
                        <td><?= Yii::$app->formatter->asDate($pembayaran->tanggal_bukti_transfer) ?></td>
                        <td><?= 'Rp' . number_format($pembayaran->jumlah_terbayar, 0, ',', '.') ?></td>
                        <td><?= 'Rp' . number_format($pembayaran->potongan_pajak, 0, ',', '.') ?></td>
                        <td><?= Html::encode($pembayaran->pemasukan->tipe_pembayaran ?? '-') ?></td>
                        <td>
                            <?= Html::encode(
                                $pembayaran->accountkeluar
                                    ? $pembayaran->accountkeluar->code . ' - ' . $pembayaran->accountkeluar->akun
                                    : '[ID: ' . ($pembayaran->accountkeluar_id ?? '-') . ']'
                            ) ?>
                        </td>
                        <td><?= $pembayaran->cicilan->ke ?? '-' ?></td>
                        <td>
                            <?php if ($pembayaran->bukti_transfer): ?>
                                <a href="#" class="btn btn-sm btn-primary preview-bukti"
                                    data-url="<?= Url::to([
                                                    '/vendorfinance/pemasukan/view-bukti',
                                                    'file' => $pembayaran->bukti_transfer,
                                                    'cicilan' => $pembayaran->pemasukan_cicilan_id ? 1 : 0
                                                ]) ?>">
                                    Lihat Bukti
                                </a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td><?= Html::encode($pembayaran->deskripsi) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <!-- Modal Preview -->
    <div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Preview Bukti Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center" id="preview-content">
                    <!-- Konten bukti akan dimuat di sini -->
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Load SweetAlert2 jika belum ada -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php
JSRegister::begin();
?>
<script>
    $(document).on("click", ".preview-bukti", function(e) {
        e.preventDefault();
        var url = $(this).data("url");
        var ext = url.split('.').pop().toLowerCase();

        var content = '';
        if (ext === 'pdf') {
            content = '<embed src="' + url + '" type="application/pdf" width="100%" height="500px">';
        } else {
            content = '<img src="' + url + '" style="max-width:100%;max-height:500px;">';
        }

        $("#preview-content").html(content);
        $("#previewModal").modal("show");
    });
    // Autofokus ketika Select2 dibuka
    $(document).on('select2:open', () => {
        document.querySelector('.select2-search__field').focus();
    });

    // Jika modal terbuka, fokus input select2
    $('#modal').on('shown.bs.modal', function() {
        setTimeout(() => {
            $('.select2-search__field').focus();
        }, 300);
    });
</script>
<?php JSRegister::end(); ?>