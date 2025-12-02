<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
use yii\web\View;
use app\widgets\JSRegister;

/* @var $model app\models\Product */
?>

<div class="product-form">

    <?php $form = ActiveForm::begin([
        'enableClientScript' => true,
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'id' => 'form-product',
        'action' => $model->isNewRecord
            ? ['create']
            : ['update', 'id_produk' => $model->id_produk],
        'formConfig' => [
            'labelSpan' => 3,
            'deviceSize' => ActiveForm::SIZE_X_SMALL,
            'options' => ['class' => 'mb-1']
        ],
    ]); ?>

    <div class="row">
        <div class="col-6">
            <?= $form->field($model, 'product_name')->textInput(['maxlength' => true, 'class' => 'form-control-sm']) ?>
        </div>
        <div class="col-6">
            <?= $form->field($model, 'code_produk')->textInput([
                'maxlength' => true,
                'class' => 'form-control-sm',
                'readonly' => true,
            ]) ?>
        </div>
        <div class="col-6">
            <?= $form->field($model, 'keterangan')->textInput(['maxlength' => true, 'class' => 'form-control-sm']) ?>
        </div>
        <?php if ($model->isNewRecord && $model->unit === null) $model->unit = 1; ?>
        <div class="col-6">
            <?= $form->field($model, 'unit')->textInput(['class' => 'form-control-sm', 'readonly'=> true]) ?>
        </div>
        <div class="col-6">
            <?= $form->field($model, 'harga')->textInput([
                'maxlength' => true,
                'class' => 'form-control-sm autonumeric',
                'autocomplete' => 'off',
            ]) ?>
        </div>
    </div>

    <hr />

    <div class="form-group d-flex flex-column align-items-end">
        <?php if ($model->isNewRecord): ?>
            <?= Html::submitButton('<i class="fa fa-save"></i> Simpan', ['class' => 'btn btn-primary btn-sm', 'id' => 'btn-save-create']) ?>
        <?php else: ?>
            <?= Html::button('<i class="fa fa-edit"></i> Ubah', ['class' => 'btn btn-warning btn-sm', 'id' => 'btn-edit']) ?>
            <?= Html::submitButton('<i class="fa fa-save"></i> Simpan', [
                'class' => 'btn btn-primary btn-sm',
                'id' => 'btn-save-edit',
                'style' => 'display:none'
            ]) ?>
        <?php endif; ?>
        <?= Html::a('<i class="fa fa-arrow-left"></i> Kembali', ['index'], ['class' => 'btn btn-secondary btn-sm mt-2']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php JSRegister::begin(['position' => View::POS_END]); ?>

<script>
    $(function() {
        <?php if ($model->isNewRecord): ?>
            // Mode Create: semua input aktif, tombol simpan muncul
            // Tidak ada disable input di create
            $('#btn-save-create').on('click', function(e) {
                e.preventDefault();
                submitForm('#form-product');
            });
        <?php else: ?>
            // Mode Edit: form disable input kecuali hidden input
            $('#form-product').find('input, textarea, select').not('[type=hidden]').prop('disabled', true);


            // tombol Edit klik -> enable input, toggle tombol simpan
            $('#btn-edit').on('click', function() {
                $('#form-product :input').not('[type=hidden]').prop('disabled', false);
                $('#code_produk').prop('readonly', true); // tetap readonly
                $('#btn-save-edit').show();
                $('#btn-edit').hide();
            });

            $('#btn-save-edit').on('click', function(e) {
                e.preventDefault();
                submitForm('#form-product');
            });
        <?php endif; ?>

        function submitForm(formId) {
            var form = $(formId);
            var url = form.attr('action');
            var data = form.serialize();

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Akan Menyimpan Data?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Lanjutkan',
                cancelButtonText: 'Batal',
            }).then(result => {
                if (result.isConfirmed) {
                    $.post(url, data)
                        .done(function(response) {
                            if (response.status === 'success') {
                                $('#modal').modal('hide');
                                Swal.fire('Sukses', response.message, 'success');
                                $.pjax.reload({
                                    container: "#gridDataproduct"
                                });
                            } else {
                                Swal.fire('Gagal', response.message, 'error');
                            }
                        })
                        .fail(function() {
                            Swal.fire('Gagal', 'Terjadi kesalahan server', 'error');
                        });
                }
            });
        }
    });
</script>
<?php JSRegister::end(); ?>