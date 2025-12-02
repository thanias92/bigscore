<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
use yii\web\View;
use app\widgets\JSRegister;

/* @var $this yii\web\View */
/* @var $model app\models\Roomchat */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="roomchat-form">

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
      <?= $form->field($model, 'id_customer')->textInput(['class' => 'form-control-sm']) ?>
    </div>
    <div class="col-4">
      <?= $form->field($model, 'id_staff')->textInput(['class' => 'form-control-sm']) ?>
    </div>
    <div class="col-4">
      <?= $form->field($model, 'chat')->textInput(['maxlength' => true, 'class' => 'form-control-sm']) ?>
    </div>
    <div class="col-4">
      <?= $form->field($model, 'send_at')->textInput(['class' => 'form-control-sm']) ?>
    </div>
    <div class="col-4">
      <?= $form->field($model, 'is_read')->checkbox() ?>
    </div>
    <div class="col-4">
      <?= $form->field($model, 'created_by')->textInput(['class' => 'form-control-sm']) ?>
    </div>
    <div class="col-4">
      <?= $form->field($model, 'updated_by')->textInput(['class' => 'form-control-sm']) ?>
    </div>
    <div class="col-4">
      <?= $form->field($model, 'deleted_by')->textInput(['class' => 'form-control-sm']) ?>
    </div>
    <div class="col-4">
      <?= $form->field($model, 'created_at')->textInput(['class' => 'form-control-sm']) ?>
    </div>
    <div class="col-4">
      <?= $form->field($model, 'updated_at')->textInput(['class' => 'form-control-sm']) ?>
    </div>
    <div class="col-4">
      <?= $form->field($model, 'deleted_at')->textInput(['class' => 'form-control-sm']) ?>
    </div>
  </div>

  <hr />

  <div class="form-group d-flex align-items-end flex-column">
    <?= Html::submitButton('<i class="fa fa-save"></i> Simpan', ['class' => 'btn btn-primary btn-sm', "id" => "btn-save"]) ?>
  </div>

  <?php ActiveForm::end(); ?>
</div>

<?php
JSRegister::begin(['position' => View::POS_END]);
?>
<script>
  $('#form').submit(function(e) {
    e.preventDefault();
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
              $.pjax.reload({ container: "#gridDataroomchat" });
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
