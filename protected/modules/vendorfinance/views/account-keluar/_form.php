<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
use yii\web\View;
use app\widgets\JSRegister;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\AccountKeluar */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="account-keluar-form">

  <?php $form = ActiveForm::begin([
    'enableClientScript' => true,
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'id' => 'form',
    'formConfig' => [
      'labelSpan' => 3,
      'deviceSize' => ActiveForm::SIZE_X_SMALL,
      'options' => ['class' => 'mb-1']
    ]
  ]);
  $akunIndukDD = ArrayHelper::map(
    app\models\Accountkeluar::find()->where(['parent_id'=>null])->orderBy('akun')->all(),
    'id','akun'
);
 ?>
  
  <div class="row">
    <div class="col-4">
      <?= $form->field($model, 'code')->textInput(['maxlength' => true, 'class' => 'form-control-sm']) ?>
    </div>
    <div class="col-4">
      <?= $form->field($model, 'akun')->textInput(['maxlength' => true, 'class' => 'form-control-sm']) ?>
    </div>
    <div class="col-md-4">
      <?= $form->field($model, 'penggunaan')
        ->dropDownList(
          ['pengeluaran' => 'Pengeluaran', 'pemasukan' => 'Pemasukan'],
          ['prompt' => '- pilih -']
        ) ?>
    </div>
    <div class="col-md-4">
      <?= $form->field($model, 'parent_id')
        ->dropDownList($akunIndukDD, ['prompt' => '- (Tidak ada) -']) ?>
    </div>
  </div>
  <hr />
  <div class="form-group d-flex align-items-end flex-column">
    <?= Html::submitButton('<i class="fa fa-save"></i> ' . Yii::t('app', 'Simpan'), ['class' => 'btn btn-primary btn-sm', "id" => "btn-save"]) ?>
  </div>

  <?php ActiveForm::end(); ?>

</div>


<?php
JSRegister::begin(['position' => View::POS_END]);
?>
<script>
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
                container: "#gridDataaccount-keluar"
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