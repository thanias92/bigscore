<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\form\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\widgets\JSRegister;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model app\models\Deals */
/* @var $customers array */
/* @var $products array */
/* @var $quotationList array */
/* @var $mode string */

$isNew    = $model->isNewRecord;
$mode     = $mode ?? ($isNew ? 'create' : 'view');
$isView   = $mode === 'view';
$isEdit   = $mode === 'edit';
$isCreate = $mode === 'create';
$disabled = $isView;

if (!$isCreate && $model->activeQuotation) {
  $model->linked_quotation_id = $model->activeQuotation->quotation_id;
}
?>

<div class="deals-form px-3 py-2">
  <?php $form = ActiveForm::begin([
    'id' => 'deals-form',
    'action' => $isCreate
      ? Url::to(['/sales/deals/create'])
      : Url::to(['/sales/deals/update', 'id' => $model->deals_id]),
    'method' => 'post',
    'enableClientValidation' => false,
    'enableAjaxValidation' => false,
  ]); ?>

  <div class="row">
    <div class="col-md-8">
      <div class="row">
        <div class="col-md-6">
          <?= $form->field($model, 'deals_code')->textInput(['readonly' => true]) ?>

          <?= $form->field($model, 'label_deals')->widget(Select2::class, [
            'data' => \app\models\Deals::getDealsLabelList(),
            'options' => ['placeholder' => 'Choose Label', 'id' => 'deals-label_deals', 'disabled' => $disabled],
            'pluginOptions' => ['allowClear' => true],
          ]) ?>
        </div>
        <div class="col-md-6">
          <?= $form->field($model, 'customer_id')->widget(Select2::class, [
            'data' => ArrayHelper::map($customers, 'customer_id', 'customer_name'),
            'initValueText' => $model->customer->customer_name ?? '',
            'options' => ['placeholder' => 'Choose Customer', 'id' => 'deals-customer_id', 'disabled' => $disabled],
            'pluginOptions' => ['allowClear' => true, 'dropdownParent' => new \yii\web\JsExpression("$('#modal')")],
          ]) ?>
          <div class="mb-3">
            <label class="form-label">Customer Email</label>
            <input type="text" id="customer_email" class="form-control" readonly>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6" id="quotation-link-field" style="display: none;">
          <?= $form->field($model, 'linked_quotation_id')->widget(Select2::class, [
            'data' => $quotationList,
            'options' => ['placeholder' => 'Pilih Quotation', 'id' => 'deals-linked_quotation_id', 'disabled' => $disabled],
            'pluginOptions' => ['allowClear' => true, 'dropdownParent' => new \yii\web\JsExpression("$('#modal')")],
          ])->label('Quotation Terkait') ?>
        </div>
      </div>

      <div class="row" id="purchase-details-container" style="display: none;">
        <div class="col-md-6">
          <?= $form->field($model, 'purchase_type')->widget(Select2::class, [
            'data' => \app\models\Deals::getPurchaseTypeList(),
            'options' => ['placeholder' => 'Choose Purchase Type', 'id' => 'deals-purchase_type', 'disabled' => $disabled],
            'pluginOptions' => ['allowClear' => true, 'dropdownParent' => new \yii\web\JsExpression("$('#modal')")],
          ]) ?>
        </div>
        <div class="col-md-6" id="purchase-date-container" style="display: none;">
          <?= $form->field($model, 'purchase_date')->input('date', ['disabled' => $disabled]) ?>
        </div>
      </div>

      <div id="order-fields" class="product-fields-container active pt-3">
        <?= $this->render('_deals_product_table', [
          'form' => $form,
          'model' => $model,
          'products' => $products,
          'disabled' => $disabled,
          'prefix' => ''
        ]) ?>
      </div>

      <?= $form->field($model, 'description')->textarea(['rows' => 4, 'disabled' => $disabled]) ?>
    </div>
    <div class="col-md-4">
      <!-- Menampilkan riwayat deal -->
      <div class="history-container p-2 border rounded" style="max-height: 580px; overflow-y: auto;">
        <?php if (!$isCreate && !empty($model->dealsHistories)): ?>
          <?= $this->render('_deals_history', ['histories' => $model->dealsHistories]) ?>
        <?php else: ?>
          <p class="text-muted text-center mt-3">History will be shown here after the deal is created.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- FOOTER -->
  <div class="form-footer d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
    <div>
      <?php if ($isEdit || $isView) : ?>
        <?= Html::button('<i class="bi bi-trash"></i>', [
          'class' => 'btn btn-outline-icon',
          'id'    => 'delete-deal-btn',
          'title' => 'Delete Deals',
          'data-id' => $model->deals_id,
          'data-url' => Url::to(['/sales/deals/delete', 'id' => $model->deals_id]),
        ]) ?>
      <?php endif; ?>
    </div>

    <div class="d-flex align-items-center gap-2">
      <?php if ($isView) : ?>
        <?= Html::button('Edit', ['class' => 'btn btn-warning', 'id' => 'btn-edit-deal']) ?>
        <?= Html::submitButton('Save', ['class' => 'btn btn-primary', 'id' => 'btn-save-deal', 'style' => 'display:none;']) ?>
      <?php else: ?>
        <?= Html::submitButton('Submit', ['class' => 'btn btn-custom-primary', 'id' => 'btn-save-deal']) ?>
      <?php endif; ?>
      <?= Html::button('Back', ['class' => 'btn btn-custom-secondary', 'onclick' => '$("#modal").modal("hide");']) ?>
    </div>
  </div>

  <?php ActiveForm::end(); ?>
</div>

<?php JSRegister::begin(['position' => View::POS_END]); ?>
<script>
  //$(document).ready(function() {
  const isCreate = <?= json_encode($isCreate) ?>;
  const form = $('#deals-form');

  // =========================================================================
  // 1. FUNGSI-FUNGSI BANTU (TIDAK BERUBAH)
  // =========================================================================
  function formatRupiah(angka) {
    if (angka === null || isNaN(parseFloat(angka))) return '';
    let number_string = parseFloat(angka).toFixed(2).replace('.', ',');
    let split = number_string.split(',');
    let sisa = split[0].length % 3;
    let rupiah = split[0].substr(0, sisa);
    let ribuan = split[0].substr(sisa).match(/\d{3}/gi);
    if (ribuan) {
      let separator = sisa ? '.' : '';
      rupiah += separator + ribuan.join('.');
    }
    rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
    return 'Rp' + rupiah;
  }

  function unformatRupiah(rupiahStr) {
    if (typeof rupiahStr !== 'string' || !rupiahStr) return 0;
    let cleanStr = rupiahStr.replace(/[^\d,\-]/g, '').replace(/,/g, '.');
    if ((cleanStr.match(/\./g) || []).length > 1) {
      let parts = cleanStr.split('.');
      cleanStr = parts.slice(0, -1).join('') + '.' + parts.slice(-1);
    }
    return parseFloat(cleanStr) || 0;
  }

  function initializeCurrencyInput(selector) {
    $(selector).each(function() {
      if ($(this).val()) $(this).val(formatRupiah($(this).val()));
    });
    $(document).on('input', selector, function() {
      // Event 'input' lebih baik dari 'keyup' untuk menangani paste, dll.
    });
  }

  function calculateAndUpdateTotal() {
    const price = unformatRupiah($('#deals-price_product').val());
    const unit = parseFloat($('#deals-unit_product').val()) || 0;
    const totalValue = price * unit;
    // Gunakan ID unik yang sudah kita perbaiki sebelumnya
    $('#deals-line-total').val(formatRupiah(totalValue));
  }

  function setInitialCustomerEmail() {
    const id = $('#deals-customer_id').val();
    if (id) {
      $.getJSON('<?= Url::to(['/sales/deals/get-info']) ?>', {
        id: id
      }, function(data) {
        $('#customer_email').val(data.email);
      });
    }
  }

  // =========================================================================
  // 2. FUNGSI UTAMA UNTUK MENGATUR STATE FORM (DIPERBARUI)
  // =========================================================================
  function updateFormState() {
    const label = $('#deals-label_deals').val();
    const isViewMode = $('#btn-edit-deal').is(':visible') && !isCreate;
    const isCreateMode = <?= json_encode($isCreate) ?>;

    const showQuotationLabels = ['Proposal Sent', 'Negotiation', 'Deal Won', 'Deal Lost'];
    if (showQuotationLabels.includes(label)) {
      $('#quotation-link-field').slideDown();
    } else {
      $('#quotation-link-field').slideUp();
    }

    const showPurchaseTypeLabels = ['Negotiation', 'Deal Won', 'Deal Lost'];
    if (showPurchaseTypeLabels.includes(label)) {
      $('#purchase-details-container').slideDown();
    } else {
      $('#purchase-details-container').slideUp();
    }

    if (label === 'Deal Won') {
      $('#purchase-date-container').slideDown();
    } else {
      $('#purchase-date-container').slideUp();
    }

    // Aturan izin edit (tidak berubah)
    const lockedLabels = ['New', 'Proposal Sent', 'Deal Lost'];
    const areUnitAndPriceLocked = lockedLabels.includes(label) || isViewMode;
    $('#deals-unit_product, #deals-price_product').prop('disabled', areUnitAndPriceLocked);

    const isProductDropdownLocked = (!isCreateMode && lockedLabels.includes(label)) || isViewMode;
    $('#deals-product_id').prop('disabled', isProductDropdownLocked);
  }

  // =========================================================================
  // 3. EVENT HANDLERS (PENANGAN INTERAKSI PENGGUNA)
  // =========================================================================

  // Saat dropdown LABEL berubah
  $('#deals-label_deals').on('change', function() {
    updateFormState(); // Cukup panggil fungsi ini, nilainya akan diwarisi secara otomatis
  });

  // Saat dropdown QUOTATION berubah
  $('#deals-linked_quotation_id').on('change', function() {
    const quotationId = $(this).val();
    const label = $('#deals-label_deals').val();
    if (quotationId && label === 'Proposal Sent') {
      $.getJSON('<?= Url::to(['/sales/deals/get-quotation-details']) ?>', {
        id: quotationId
      }, function(response) {
        if (response.status === 'success') {
          $('#deals-product_id').val(response.product_id).trigger('change.select2');
          $('#deals-unit_product').val(response.unit);
          $('#deals-price_product').val(formatRupiah(response.price));
          calculateAndUpdateTotal();
        }
      });
    }
  });

  // Saat dropdown PRODUK berubah
  $('#deals-product_id').on('change', function() {
    if (!$(this).is(':disabled')) {
      const productId = $(this).val();
      if (productId) {
        $.getJSON('<?= Url::to(['/sales/deals/get-product-info']) ?>', {
          id: productId
        }, function(data) {
          $('#deals-unit_product').val(data.unit || 1);
          $('#deals-price_product').val(formatRupiah(data.price || 0));
          calculateAndUpdateTotal();
        });
      }
    }
  });

  // Event handler lain (tidak berubah)
  $(document).on('input', '#deals-unit_product, #deals-price_product', calculateAndUpdateTotal);
  $('#deals-customer_id').on('change', setInitialCustomerEmail);
  $('#btn-edit-deal').on('click', function() {
    form.find('input, select, textarea').not('[readonly]').prop('disabled', false);
    $('#btn-save-deal').show();
    $(this).hide();
    $('#modalHeader .modal-title').text('Update Deal');
    updateFormState();
  });

  // =========================================================================
  // 4. LOGIKA SUBMIT DAN DELETE (AJAX) - (TIDAK BERUBAH)
  // =========================================================================
  form.on('beforeSubmit', function(e) {
    e.preventDefault();
    var form = $(this);
    const originalValues = {};
    $('.currency-input').each(function() {
      originalValues[this.id] = $(this).val();
      $(this).val(unformatRupiah($(this).val()));
    });
    let swalConfig = {
      icon: null,
      showCancelButton: true,
      reverseButtons: true,
      confirmButtonColor: '#27465E',
      customClass: {
        popup: 'swal2-noicon',
        confirmButton: 'btn btn-custom-primary',
        cancelButton: 'btn btn-outline-custom'
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
        $.ajax({
          url: form.attr('action'),
          type: form.attr('method'),
          data: form.serialize(),
          dataType: 'json',
          success: function(response) {
            if (response.status === 'success') {
              $('#modal').modal('hide');
              Swal.fire('Success!', response.message, 'success');
              $.pjax.reload({
                container: '#gridDatadeals',
              });
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
          },
          complete: function() {
            for (const id in originalValues) {
              $('#' + id).val(originalValues[id]);
            }
          }
        });
      } else {
        for (const id in originalValues) {
          $('#' + id).val(originalValues[id]);
        }
      }
    });
    return false;
  });

  $('#delete-deal-btn').on('click', function() {
    const url = $(this).data('url');
    Swal.fire({
      title: 'DELETE DATA?',
      text: "Do you want to delete this data?",
      icon: null,
      showCancelButton: true,
      reverseButtons: true,
      confirmButtonText: 'Yes, delete data',
      cancelButtonText: 'No, cancel',
      confirmButtonColor: '#27465E',
      customClass: {
        popup: 'swal2-noicon',
        confirmButton: 'btn btn-danger',
        cancelButton: 'btn btn-outline-secondary'
      },
      buttonsStyling: false
    }).then((result) => {
      // Blok AJAX di sini (TIDAK PERLU DIUBAH)
      if (result.isConfirmed) {
        $.ajax({
          url: url,
          type: 'POST',
          dataType: 'json',
          success: function(response) {
            if (response.status === 'success') {
              $('#modal').modal('hide');
              Swal.fire('Deleted!', response.message, 'success');
              $.pjax.reload({
                container: '#gridDatadeals',
              });
            } else {
              Swal.fire('Error!', response.message, 'error');
            }
          },
          error: function() {
            Swal.fire('Error!', 'A server error occurred.', 'error');
          }
        });
      }
    });
  });

  // =========================================================================
  // 5. INISIALISASI SAAT FORM DI-LOAD
  // =========================================================================
  setInitialCustomerEmail();
  initializeCurrencyInput('.currency-input');
  updateFormState();
  if (!isCreate) {
    calculateAndUpdateTotal();
  }
</script>
<?php JSRegister::end(); ?>