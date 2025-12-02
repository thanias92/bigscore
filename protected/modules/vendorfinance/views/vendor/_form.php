<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
use yii\web\View;
use app\widgets\JSRegister;

/* @var $this yii\web\View */
/* @var $model app\models\Vendor */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="vendor-form">

  <?php $form = ActiveForm::begin([
    'enableClientScript' => true,
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'id' => 'form',
    'formConfig' => [
      'labelSpan' => 3,
      'deviceSize' => ActiveForm::SIZE_X_SMALL,
      'options' => ['class' => 'mb-1']
    ]
  ]); ?>
  <div class="row">
    <div class="col-4">
      <?= $form->field($model, 'nama_vendor')->textInput(['maxlength' => true, 'class' => 'form-control-sm']) ?>
    </div>
    <div class="col-4">
      <?= $form->field($model, 'alamat_vendor')->textInput(['maxlength' => true, 'class' => 'form-control-sm']) ?>
    </div>
    <div class="col-4">
      <?= $form->field($model, 'email_vendor')->textInput(['maxlength' => true, 'class' => 'form-control-sm']) ?>
    </div>
    <div class="col-4">
      <?= $form->field($model, 'telp_vendor')->textInput([
        'class' => 'form-control-sm',
        'maxlength' => 15,
        'inputmode' => 'tel',
        'pattern' => '0[0-9]{9,14}', // mulai dari 0, minimal 10 digit
        'placeholder' => '08xxxxxxxxxx'
      ]) ?>
    </div>
    <div class="col-4">
      <?= $form->field($model, 'nama_PIC')->textInput(['maxlength' => true, 'class' => 'form-control-sm']) ?>
    </div>
    <div class="col-4">
      <?= $form->field($model, 'email_PIC')->textInput(['maxlength' => true, 'class' => 'form-control-sm']) ?>
    </div>
    <div class="col-4">
      <?= $form->field($model, 'telp_PIC')->textInput([
        'class' => 'form-control-sm',
        'maxlength' => 15,
        'inputmode' => 'tel',
        'pattern' => '0[0-9]{9,14}',
        'placeholder' => '08xxxxxxxxxx'
      ]) ?>
    </div>
  </div>

  <hr />
  <div class="form-group d-flex align-items-end flex-column">
    <?= Html::submitButton('<i class="fa fa-save"></i> ' . 'Simpan', ['class' => 'btn btn-primary btn-sm', "id" => "btn-save"]) ?>
  </div>

  <?php ActiveForm::end(); ?>

</div>
<?php
JSRegister::begin(['position' => View::POS_END]);
?>
<script>
  // Hapus validasi angka-only, karena awalan 0 dan string diizinkan
  $('#form').submit(function(e) {
    e.preventDefault()
  });

  $(document).on("click", "#btn-save", function() {
    var link_simpan = $('#form').attr('action');
    var data_form = $('#form').serializeArray();

    Swal.fire({
      title: 'Konfirmasi',
      text: 'Apakah Akan Menyimpan Data?',
      showCancelButton: true,
      confirmButtonText: 'Lanjutkan',
      cancelButtonText: 'Batal',
      icon: 'question',
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          type: "POST",
          url: link_simpan,
          data: data_form,
          dataType: "html",
          success: function(response) {
            const res = JSON.parse(response);
            if (res.status == 'success') {
              $('#modal').modal('hide');
              Swal.fire('OK', res.message, 'success');
              $.pjax.reload({
                container: "#gridDatavendor"
              });
            } else {
              Swal.fire('Terjadi Kesalahan.', res.message, 'warning');
            }
          }
        });
      }
    })
  });
</script>
<?php JSRegister::end(); ?>
