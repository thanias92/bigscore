<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\form\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\widgets\JSRegister;
use yii\web\View;

$isNew    = $model->isNewRecord;
$mode     = $mode ?? ($isNew ? 'create' : 'view');
$isView   = $mode === 'view';
$isEdit   = $mode === 'edit';
$isCreate = $mode === 'create';
$disabled = $isView;
?>

<div class="contract-form px-3 py-2">
  <?php $form = ActiveForm::begin([
    'id' => 'contract-form',
    'action' => $isNew ? ['create'] : ['update', 'contract_id' => $model->contract_id],
    'method' => 'post',
    'options' => ['enctype' => 'multipart/form-data']
  ]); ?>

  <div class="row">
    <div class="col-md-8">
      <div class="row">
        <div class="col-md-6">
          <?= $form->field($model, 'contract_code')->textInput(['readonly' => true]) ?>
        </div>
        <div class="col-md-6">
          <?= $form->field($model, 'invoice_id')->widget(Select2::class, [
            'data' => ArrayHelper::map($pemasukans, 'pemasukan_id', function ($p) {
              foreach ($p->cicilanAnak as $child) {
                if ($child->cicilanPertama) return $child->no_faktur;
              }
              return $p->no_faktur ?? '(No Faktur)';
            }),
            'options' => ['placeholder' => 'Choose Invoice', 'id' => 'invoice_id', 'disabled' => $disabled || !$isNew],
            'pluginOptions' => ['allowClear' => true, 'dropdownParent' => new \yii\web\JsExpression("$('#modal')")],
          ]) ?>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6">
          <label class="form-label">Customer</label>
          <input type="text" id="customer_name" class="form-control mb-3" readonly>
        </div>
        <div class="col-md-6">
          <label class="form-label">Customer Email</label>
          <input type="text" id="customer_email" class="form-control mb-3" readonly>
        </div>
      </div>

      <?= $this->render('_contract_product_table', [
        'model' => $model,
        'form' => $form // <-- Kirim variabel $form
      ]) ?>

      <div class="row mt-3">
        <div class="col-md-6">
          <?= $form->field($model, 'start_date')->input('date', ['value' => !empty($model->start_date) ? Yii::$app->formatter->asDate($model->start_date, 'php:Y-m-d') : '', 'disabled' => $disabled]) ?>
        </div>
        <div class="col-md-6">
          <?= $form->field($model, 'end_date')->input('date', ['value' => !empty($model->end_date) ? Yii::$app->formatter->asDate($model->end_date, 'php:Y-m-d') : '', 'disabled' => $disabled]) ?>
        </div>
      </div>

      <div class="form-group field-contract-status_contract mb-3">
        <label class="form-label d-block">Status Contract</label>
        <div class="toggle-switch-container">
          <label class="toggle-switch">
            <?= Html::activeCheckbox($model, 'status_contract', [
              'label' => false,
              'value' => 'Active',
              'uncheck' => 'Inactive',
              'checked' => $isNew ? false : ($model->status_contract === 'Active'),
              'disabled' => $disabled,
            ]) ?>
            <span class="slider"></span>
          </label>
          <span id="status-label" class="toggle-switch-label">
            <?= $isNew ? 'Inactive' : ($model->status_contract === 'Active' ? 'Active' : 'Inactive') ?>
          </span>
        </div>
      </div>

      <div class="row mt-2">
        <div class="col-md-6">
          <?= $form->field($model, 'description')->textarea(['rows' => 4, 'disabled' => $disabled]) ?>
        </div>
        <div class="col-md-6">
          <?= $form->field($model, 'uploadFile')->fileInput(['accept' => '.pdf', 'disabled' => $disabled]) ?>
          <div id="pdf-preview-container" class="mt-2" style="display:none;">
            <iframe id="pdf-preview" src="" width="100%" height="200px" class="border rounded"></iframe>
          </div>
          <?php if (!$isNew && $model->evidence_contract): ?>
            <div class="mt-2" id="existing-pdf-container">
              <iframe src="<?= Url::to('@web/' . $model->evidence_contract) ?>" width="100%" height="200px" class="border rounded"></iframe>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <?= $this->render('_contract_history', ['histories' => $model->isNewRecord ? [] : $model->contractHistories]) ?>
    </div>
  </div>
  <div class="form-footer d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
    <div>
      <?php if (!$isNew): ?>
        <?= Html::button('<i class="bi bi-trash"></i>', ['class' => 'btn btn-outline-danger', 'title' => 'Delete Contract', 'id' => 'btn-delete-contract', 'data-id' => $model->contract_id]) ?>
      <?php endif; ?>
    </div>
    <div class="d-flex align-items-center gap-2">
      <?php if ($isView): ?>
        <?= Html::button('Edit', ['class' => 'btn btn-warning', 'id' => 'btn-edit-contract']) ?>
        <?= Html::submitButton('Save', ['class' => 'btn btn-custom-primary', 'id' => 'btn-save-contract', 'style' => 'display:none;']) ?>
      <?php else: ?>
        <?= Html::submitButton('Submit', ['class' => 'btn btn-custom-primary']) ?>
      <?php endif; ?>
      <?= Html::button('Back', ['class' => 'btn btn-custom-secondary', 'onclick' => '$("#modal").modal("hide");']) ?>
    </div>
  </div>
  <?php ActiveForm::end(); ?>
</div>
<?php JSRegister::begin(['position' => View::POS_END]); ?>
<script>
  $(document).ready(function() {
    const form = $('#contract-form');
    const isCreate = <?= json_encode($isCreate) ?>;

    // FUNGSI UNTUK MENGISI DATA OTOMATIS
    function loadInvoiceData() {
      const selectedId = $('#invoice_id').val();
      // Reset fields
      $('#customer_name, #customer_email, #contract-product-name, #contract-unit, #contract-price, #contract-total').val('');
      if (!selectedId) return;

      $.getJSON('<?= Url::to(['get-info']) ?>', {
        id: selectedId
      }, function(data) {
        if (data.error) return;

        const formatter = new Intl.NumberFormat('id-ID', {
          style: 'currency',
          currency: 'IDR'
        });

        $('#customer_name').val(data.customer_name || '');
        $('#customer_email').val(data.email || '');
        // Mengisi field di tabel produk
        $('#contract-product-name').val(data.product || '');
        $('#contract-unit').val(data.unit || '');
        $('#contract-price').val(formatter.format(data.price || 0));
        $('#contract-total').val(formatter.format(data.total || 0));
      });
    }

    // Panggil fungsi saat halaman dimuat (untuk mode view/edit) dan saat dropdown berubah
    if ($('#invoice_id').val()) loadInvoiceData();
    $('#invoice_id').on('change', loadInvoiceData);

    // LOGIKA TOGGLE SWITCH
    const statusCheckbox = $('#contract-status_contract');
    const statusLabel = $('#status-label');
    statusCheckbox.on('change', function() {
      statusLabel.text($(this).is(':checked') ? 'Active' : 'Inactive');
    });

    $('#btn-edit-contract').on('click', function() {
      form.find('input, select, textarea').not('[readonly]').prop('disabled', false);
      $('#invoice_id').prop('disabled', true); // Invoice tidak boleh diedit
      $('#btn-save-contract').show();
      $(this).hide();
    });

    $('#contract-uploadfile').on('change', function(event) {
      const file = event.target.files[0];
      if (file && file.type === "application/pdf") {
        const fileURL = URL.createObjectURL(file);
        $('#pdf-preview').attr('src', fileURL);
        $('#pdf-preview-container').show();
      } else {
        $('#pdf-preview-container').hide();
      }
    });

    form.on('beforeSubmit', function(e) {
      e.preventDefault();
      var formData = new FormData(this);

      let swalConfig = {
        icon: null,
        showCancelButton: true,
        reverseButtons: true,
        customClass: {
          popup: 'swal2-noicon',
          confirmButton: 'btn btn-custom-primary',
          cancelButton: 'btn btn-outline-custom'
        },
        buttonsStyling: false
      };

      if (isCreate) {
        swalConfig.title = 'SAVE DATA?';
        swalConfig.text = 'Do you want to save this new contract?';
        swalConfig.confirmButtonText = 'Yes, save it!';
        swalConfig.cancelButtonText = 'No, cancel';
      } else {
        swalConfig.title = 'UPDATE DATA?';
        swalConfig.text = 'Do you want to save the changes to this contract?';
        swalConfig.confirmButtonText = 'Yes, update it!';
        swalConfig.cancelButtonText = 'No, cancel';
      }

      Swal.fire(swalConfig).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                $('#modal').modal('hide');
                $.pjax.reload({
                  container: "#gridDatacontract",
                  timeout: 3000
                });
                Swal.fire('Success!', response.message, 'success');
              } else {
                let errorMsg = response.message || 'An error occurred.';
                if (response.errors) {
                  errorMsg += '<ul class="text-start">';
                  $.each(response.errors, (key, value) => {
                    errorMsg += `<li>${value.join(', ')}</li>`;
                  });
                  errorMsg += '</ul>';
                }
                Swal.fire('Error!', errorMsg, 'error');
              }
            },
            error: function() {
              Swal.fire('Error!', 'A server error occurred.', 'error');
            }
          });
        }
      });
      return false;
    });

    $('#btn-delete-contract').on('click', function() {
      const id = $(this).data('id');
      Swal.fire({
        title: 'DELETE DATA?',
        text: "This action cannot be undone!",
        icon: null,
        showCancelButton: true,
        reverseButtons: true,
        confirmButtonText: 'Yes, delete it!',
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
            url: '<?= Url::to(['delete']) ?>?contract_id=' + id,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
              if (response.success) { // Pastikan ini sudah diperbaiki dari prompt sebelumnya
                $('#modal').modal('hide');
                $.pjax.reload({
                  container: "#gridDatacontract",
                  timeout: 3000
                });
                Swal.fire('Deleted!', response.message, 'success');
              } else {
                Swal.fire('Error!', response.message, 'error');
              }
            }
          });
        }
      });
    });
  });
</script>
<?php JSRegister::end(); ?>