<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\Customer;
use app\widgets\JSRegister;
use yii\web\View;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\Customer */
/* @var $mode string */

$isNew    = $model->isNewRecord;
$mode     = $mode ?? ($isNew ? 'create' : 'view');
$isView   = $mode === 'view';
$isEdit   = $mode === 'edit';
$isCreate = $mode === 'create';
$disabled = $isView;
?>

<div class="customer-form px-3 py-2">
  <?php $form = ActiveForm::begin([
    'id' => 'customer-form',
    'action' => $isCreate ? ['create'] : ['update', 'customer_id' => $model->customer_id],
    'method' => 'post',
  ]); ?>

  <div class="row">
    <div class="col-md-8">
      <div class="row align-items-center mb-3">
        <div class="col-md-5">
          <?= $form->field($model, 'customer_code', ['options' => ['class' => 'mb-0']])->textInput(['readonly' => true]) ?>
        </div>
        <div class="col-md-7 text-end">
          <?php if (!$isCreate) :
          ?>
            <?= Html::button('<i class="bi bi-calendar-plus"></i> Log Visit', [
              'class' => 'btn btn-primary',
              'id' => 'btn-log-visit',
              'data-customer-id' => $model->customer_id,
            ]) ?>
          <?php endif; ?>
        </div>
      </div>

      <?= $form->field($model, 'customer_name')->textInput(['maxlength' => true, 'disabled' => $disabled]) ?>
      <?= $form->field($model, 'customer_email')->textInput(['maxlength' => true, 'type' => 'email', 'disabled' => $disabled]) ?>

      <div class="row">
        <div class="col-md-7">
          <?= $form->field($model, 'customer_website')->textInput(['maxlength' => true, 'disabled' => $disabled]) ?>
        </div>
        <div class="col-md-5">
          <?= $form->field($model, 'establishment_date')->input('date', ['value' => !empty($model->establishment_date) ? Yii::$app->formatter->asDate($model->establishment_date, 'php:Y-m-d') : '', 'disabled' => $disabled])->label('Incorporation Date') ?>
        </div>
      </div>

      <?= $form->field($model, 'customer_phone')->textInput(['maxlength' => true, 'disabled' => $disabled]) ?>

      <?= $form->field($model, 'customer_address')->textarea(['rows' => 3, 'disabled' => $disabled]) ?>


      <div class="contact-person-section mt-4">CONTACT PERSON</div>
      <div class="contact-person-divider"></div>

      <div class="col-md-5">
        <?= $form->field($model, 'customer_source')->widget(Select2::class, [
          'data' => Customer::getSourceList(),
          'options' => [
            'placeholder' => 'Choose Source',
            'disabled' => $disabled
          ],
          'pluginOptions' => [
            'allowClear' => true,
            'dropdownParent' => new \yii\web\JsExpression("$('#modal')")
          ],
        ])->label('Source') ?>
      </div>

      <div class="row">
        <div class="col-md-6">
          <?= $form->field($model, 'pic_name')->textInput(['maxlength' => true, 'disabled' => $disabled])->label('Name') ?>
          <?= $form->field($model, 'pic_email')->textInput(['maxlength' => true, 'type' => 'email', 'disabled' => $disabled])->label('Email') ?>
        </div>
        <div class="col-md-6">
          <?= $form->field($model, 'pic_workroles')->textInput(['maxlength' => true, 'disabled' => $disabled])->label('Position') ?>
          <?= $form->field($model, 'pic_phone')->textInput(['maxlength' => true, 'disabled' => $disabled])->label('Phone') ?>
        </div>
      </div>

    </div>

    <div class="col-md-4">
      <div class="history-section-wrapper">
        <h5 class="mb-3">Customer History</h5>

        <?= $this->render('_customer_history', ['histories' => $isNew ? [] : $model->getUnifiedHistory()]) ?>
      </div>
    </div>
  </div>

  <div class="form-footer d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
    <div>
      <?php if ($isEdit || $isView): ?>
        <?= Html::button('<i class="bi bi-trash"></i>', [
          'class' => 'btn btn-outline-danger btn-delete-customer',
          'title' => 'Delete Customer',
          'data-id' => $model->customer_id,
        ]) ?>
      <?php endif; ?>
    </div>
    <div class="d-flex align-items-center gap-2">
      <?php if ($isView): ?>
        <?= Html::button('Edit', ['class' => 'btn btn-warning', 'id' => 'btn-edit-customer']) ?>

        <?= Html::submitButton('Save', ['class' => 'btn btn-primary', 'id' => 'btn-save-customer', 'style' => 'display:none;']) ?>

      <?php else: ?>
        <?= Html::submitButton('Submit', ['class' => 'btn btn-custom-primary']) ?>
      <?php endif; ?>

      <?= Html::button('Back', ['class' => 'btn btn-custom-secondary', 'onclick' => '$("#modal").modal("hide");']) ?>
    </div>
  </div>

  <?php ActiveForm::end(); ?>
</div>

<?php
\yii\bootstrap5\Modal::begin([
  'id' => 'visit-modal',
  'title' => '<h5>Log New Customer Visit</h5>',
  'footer' => Html::button('Cancel', ['class' => 'btn btn-secondary', 'data-bs-dismiss' => 'modal']) .
    Html::button('Save Visit', ['class' => 'btn btn-primary', 'id' => 'btn-save-visit']),
]);
echo '<div id="visit-modal-content"><div class="text-center p-5"><div class="spinner-border"></div></div></div>';
\yii\bootstrap5\Modal::end();
?>

<?php JSRegister::begin(['position' => View::POS_END]); ?>
<script>
  // Tombol Edit di dalam form
  $('#btn-edit-customer').on('click', function() {
    $('#customer-form').find('input, select, textarea').not('[readonly]').prop('disabled', false);
    // Khusus untuk Select2, kita perlu enable secara terpisah
    $('#customer-customer_source').prop('disabled', false).trigger('change');
    $('#btn-save-customer').show();
    $(this).hide(); // Sembunyikan tombol edit
  });

  // Submit form (Create & Update)
  $('#customer-form').on('beforeSubmit', function(e) {
    e.preventDefault();
    var form = $(this);
    const isCreate = <?= json_encode($isCreate) ?>;

    // === AWAL BLOK SWAL BARU ===
    let swalConfig = {
      icon: null,
      showCancelButton: true,
      reverseButtons: true,
      confirmButtonColor: '#27465E',
      customClass: {
        popup: 'swal2-noicon',
        confirmButton: 'btn btn-custom-primary',
        cancelButton: 'btn btn-outline-secondary'
      },
      buttonsStyling: false
    };

    if (isCreate) {
      swalConfig.title = 'SAVE DATA?';
      swalConfig.text = 'Do you want to save this data?';
      swalConfig.confirmButtonText = 'Yes, save data';
      swalConfig.cancelButtonText = 'No, cancel';
    } else {
      swalConfig.title = 'EDIT DATA?';
      swalConfig.text = 'Do you want to change this data?';
      swalConfig.confirmButtonText = 'Yes, edit data';
      swalConfig.cancelButtonText = 'No, cancel';
    }

    Swal.fire(swalConfig).then((result) => {
      if (result.isConfirmed) {
        // === AWAL BLOK AJAX ===
        $.ajax({
          url: form.attr('action'),
          type: form.attr('method'),
          data: form.serialize(),
          dataType: 'json',
          success: function(response) {
            if (response.success) {
              $('#modal').modal('hide');

              // Perintah untuk me-refresh tabel Pjax
              $.pjax.reload({
                container: '#grid-customer-pjax',
                timeout: 3000 // Waktu tunggu
              });

              // Notifikasi sukses setelah refresh
              Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: response.message,
                showConfirmButton: false,
                timer: 1500 // Notifikasi hilang setelah 1.5 detik
              });
            } else {
              // Jika ada error validasi, muat ulang konten modal dengan error
              $('#modalContent').html(response.content);
            }
          },
          error: function() {
            Swal.fire('Error!', 'An error occurred.', 'error');
          }
        });
      }
    });

    return false;
  }).on('submit', function(e) {
    e.preventDefault();
  });

  // Tombol Delete
  $('.btn-delete-customer').on('click', function(e) {
    e.preventDefault();
    var url = '<?= Url::to(['delete']) ?>?customer_id=' + $(this).data('id');
    Swal.fire({
      title: 'DELETE DATA?',
      text: "Do you want to delete this data?",
      icon: null,
      showCancelButton: true,
      reverseButtons: true,
      confirmButtonText: 'Yes, delete data',
      cancelButtonText: 'No, cancel',
      customClass: {
        popup: 'swal2-noicon',
        confirmButton: 'btn btn-danger',
        cancelButton: 'btn btn-outline-secondary'
      },
      buttonsStyling: false
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: url,
          type: 'POST',
          dataType: 'json',
          success: function(response) {
            if (response.success) {
              $('#modal').modal('hide');
              $.pjax.reload({
                container: '#grid-customer-pjax',
              });
              Swal.fire({
                icon: 'success',
                title: 'Deleted!',
                text: response.message,
                showConfirmButton: false,
              });
            } else {
              Swal.fire('Error!', response.message, 'error');
            }
          }
        });
      }
    });
  });

  // === JAVASCRIPT BARU UNTUK LOG VISIT ===
  $('#btn-log-visit').on('click', function() {
    const customerId = $(this).data('customer-id');
    const url = "<?= Url::to(['/sales/customer/log-visit']) ?>?customer_id=" + customerId;

    // Tampilkan modal dan muat form
    $('#visit-modal').modal('show');
    $('#visit-modal-content').html('<div class="text-center p-5"><div class="spinner-border"></div></div>');
    $('#visit-modal-content').load(url);
  });

  $('#btn-save-visit').on('click', function() {
    const form = $('#visit-form');
    const actionUrl = form.attr('action');

    $.ajax({
      url: actionUrl,
      type: 'post',
      data: form.serialize(),
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          $('#visit-modal').modal('hide');

          // Refresh konten modal utama untuk menampilkan history terbaru
          $('#modalContent').load('<?= Url::to(['view-modal', 'customer_id' => $model->customer_id]) ?>');

          Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: response.message,
            showConfirmButton: false,
            timer: 1500
          });
        } else {
          Swal.fire('Error!', 'Failed to save visit. Please check the form.', 'error');
        }
      },
      error: function() {
        Swal.fire('Error!', 'A server error occurred.', 'error');
      }
    });
  });
</script>
<?php JSRegister::end(); ?>