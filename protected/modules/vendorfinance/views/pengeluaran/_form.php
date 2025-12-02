<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
use yii\web\View;
use kartik\select2\Select2;
use app\widgets\JSRegister;
use kartik\date\DatePicker;
use kartik\number\NumberControl;

/* @var $this yii\web\View */
/* @var $model app\models\Pengeluaran */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pengeluaran-form">

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
    <div class="col-6">
      <?= $form->field($model, 'tanggal')->widget(DatePicker::class, [
        'options' => [
          'autocomplete' => 'off',
          'class' => 'form-control form-control-sm',
        ],
        'pluginOptions' => [
          'autoclose' => true,
          'format' => 'dd/mm/yyyy',
          'todayHighlight' => true,
        ],
      ]) ?>
    </div>

    <div class="col-6">
      <?= $form->field($model, 'no_pengeluaran')->textInput([
        'maxlength' => true,
        'class' => 'form-control-sm',
        'readonly' => true
      ]) ?>
    </div>

    <div class="col-6">
      <?= $form->field($model, 'accountkeluar_id')->widget(Select2::classname(), [
        'data' => $listAccount,
        'options' => ['placeholder' => 'Pilih Akun...'],
        'pluginOptions' => [
          'allowClear' => true,
          'theme' => Select2::THEME_KRAJEE_BS5,
          'dropdownParent' => new \yii\web\JsExpression("$('#modal')"),
        ],
      ]) ?>
    </div>
    <div class="col-6">
      <?= $form->field($model, 'jenis_pembayaran')->dropDownList(
        ['Transfer Bank' => 'Transfer Bank', 'Cash' => 'Cash'],
        ['class' => 'form-control form-control-sm', 'prompt' => '- Pilih Jenis Pembayaran -']
      ) ?>
    </div>

    <div class="col-6">
      <?= $form->field($model, 'jumlah')->textInput([
        'class' => 'form-control form-control-sm',
        'id' => 'jumlah-uang',
        'autocomplete' => 'off',
      ]) ?>

      <!-- hidden input untuk submit nilai yang benar -->
      <?= Html::hiddenInput('Pengeluaran[jumlah]', '', ['id' => 'jumlah-asli']) ?>
    </div>

    <?php
    $vendorText = '';
    if ($model->id_vendor) {
      $vendor = \app\models\Vendor::findOne($model->id_vendor);
      $vendorText = $vendor ? $vendor->nama_vendor : '';
    }
    ?>
    <div class="col-6">
      <?= $form->field($model, 'id_vendor')->widget(Select2::classname(), [
        'initValueText' => $vendorText,
        'data' => $listVendor,
        'options' => [
          'placeholder' => 'Pilih Vendor ...',
        ],
        'pluginOptions' => [
          'allowClear' => true,
          'theme' => Select2::THEME_KRAJEE_BS5,
          'dropdownParent' => new \yii\web\JsExpression("$('#modal')"),
        ],
      ]) ?>

    </div>
    <div class="col-6">
      <?= $form->field($model, 'keterangan')->textInput(['maxlength' => true, 'class' => 'form-control form-control-sm']) ?>
    </div>
  </div>
  <hr />
  <div class="form-group d-flex align-items-end flex-column">
    <?= Html::submitButton('<i class="fa fa-save"></i> ' . Yii::t('app', 'Simpan'), ['class' => 'btn btn-primary btn-sm', "id" => "btn-save"]) ?>
  </div>

  <?php ActiveForm::end(); ?>

</div>
<!-- Panggil library InputMask DI LUAR JSRegister -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.8/jquery.inputmask.min.js"></script>

<?php
JSRegister::begin(['position' => View::POS_END]);
?>
<script>
  function initUangMask() {
    $('#jumlah-uang').inputmask({
      alias: 'numeric',
      groupSeparator: '.',
      autoGroup: true,
      digits: 0,
      rightAlign: false,
      placeholder: '0',
      allowMinus: false,
      integerDigits: 20,
    });
  }

  $(document).ready(function() {
    initUangMask();
  });

  $('#modal').on('shown.bs.modal', function() {
    initUangMask();
    setTimeout(function() {
      $('.select2-search__field').focus();
    }, 300);
  });

  $(document).on('select2:open', () => {
    document.querySelector('.select2-search__field').focus();
  });

  $('#form').submit(function(e) {
    e.preventDefault();
  });

  $(document).on("click", "#btn-save", function() {
    var link_simpan = $('#form').attr('action');

    // Ambil nilai tanpa titik dari inputmask dan isi ke hidden
    var raw_jumlah = $('#jumlah-uang').inputmask('unmaskedvalue');
    $('#jumlah-asli').val(raw_jumlah);

    // Serialize semua form (termasuk hidden)
    var data_form = $('#form').serialize();

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
                container: "#gridDatapengeluaran"
              });
            } else {
              Swal.fire('Terjadi Kesalahan.', res.message, 'warning');
            }
          }
        });
      }
    });
  });
</script>

<?php JSRegister::end(); ?>