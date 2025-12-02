<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\form\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\widgets\JSRegister;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model app\models\Quotation */
/* @var $customers array */
/* @var $products array */
/* @var $mode string */

$isNew    = $model->isNewRecord;
$mode     = $mode ?? 'view';
$isView   = $mode === 'view';
$isEdit   = $mode === 'edit';
$isCreate = $mode === 'create';
$disabled = $isView;
?>

<div class="quotation-form px-3 py-2">
  <?php $form = ActiveForm::begin([
    'id'     => 'quotation-form',
    'action' => $isNew
      ? Url::to(['/sales/quotation/create'])
      : Url::to(['/sales/quotation/update', 'quotation_id' => $model->quotation_id]),
    'method' => 'post',
  ]); ?>

  <div class="row">
    <div class="col-md-8">
      <!-- Info Utama -->
      <div class="row">
        <div class="col-md-7">
          <?= $form->field($model, 'quotation_code')->textInput(['readonly' => true]) ?>
          <?= $form->field($model, 'customer_id')->widget(Select2::class, [
            'data' => ArrayHelper::map($customers, 'customer_id', 'customer_name'),
            'options' => ['placeholder' => 'Choose Customer', 'id' => 'customer_id', 'disabled' => $disabled],
            'pluginOptions' => ['allowClear' => true,],
          ]) ?>

          <?php if ($isView): ?>
            <?php if ($model->activeDeal): ?>
              <div class="mb-3">
                <label class="form-label">Associated Deal</label>
                <input type="text" class="form-control" readonly value="<?= Html::encode($model->activeDeal->deals_code . ' - ' . $model->activeDeal->customer->customer_name) ?>">
              </div>
            <?php endif; ?>
          <?php else: // Mode Create dan Edit 
          ?>
            <?= $form->field($model, 'linked_deal_id')->widget(Select2::class, [
              'data' => $dealsList,
              'options' => ['placeholder' => 'Link to existing Deal...'],
              'pluginOptions' => ['allowClear' => true],
              // Set nilai awal jika sedang mengedit
              'value' => $model->activeDeal->deals_id ?? null
            ])->label('Associated Deal (Optional)') ?>
          <?php endif; ?>

          <div class="mb-3">
            <label class="form-label">Customer Email</label>
            <input type="text" id="customer_email" class="form-control" readonly>
          </div>
        </div>
        <div class="col-md-5">
          <?= $form->field($model, 'quotation_status')->dropDownList(\app\models\Quotation::getQuotationStatusList(), ['disabled' => $disabled]) ?>
          <?= $form->field($model, 'created_date')->input('date', ['value' => !empty($model->created_date) ? Yii::$app->formatter->asDate($model->created_date, 'php:Y-m-d') : '', 'disabled' => $disabled]) ?>
          <?= $form->field($model, 'expiration_date')->input('date', ['value' => !empty($model->expiration_date) ? Yii::$app->formatter->asDate($model->expiration_date, 'php:Y-m-d') : '', 'disabled' => $disabled]) ?>
        </div>
      </div>

      <!-- Bagian Produk dengan TABS -->
      <div class="product-tabs">
        <button type="button" class="product-tab active" data-tab="order">Order Lines</button>
        <button type="button" class="product-tab" data-tab="optional">Optional Products</button>
      </div>

      <!-- Kontainer untuk Order Lines -->
      <div id="order-fields" class="product-fields-container active">
        <?= $this->render('_product_table', [
          'form' => $form,
          'model' => $model,
          'products' => $products,
          'disabled' => $disabled,
          'prefix' => ''
        ]) ?>
      </div>

      <!-- Kontainer untuk Optional Products -->
      <div id="optional-fields" class="product-fields-container">
        <?= $this->render('_product_table', [
          'form' => $form,
          'model' => $model,
          'products' => $products,
          'disabled' => $disabled,
          'prefix' => 'optional_'
        ]) ?>
      </div>

      <!-- Bagian Deskripsi -->
      <?= $form->field($model, 'description')->textarea(['rows' => 4, 'disabled' => $disabled]) ?>
    </div>
    <div class="col-md-4">
      <?= $this->render('_quotation_history', ['histories' => $isNew ? [] : $model->quotationHistories]) ?>
    </div>
  </div>

  <!-- FOOTER -->
  <div class="form-footer d-flex justify-content-between align-items-center">
    <div>
      <?php if ($isEdit || $isView) : ?>
        <?= Html::button('<i class="bi bi-trash"></i>', [
          'class' => 'btn btn-delete-icon',
          'id'      => 'delete-quotation-btn',
          'title' => 'Delete Quotation',
          'data-id' => $model->quotation_id,
        ]) ?>
      <?php endif; ?>
    </div>
    <div class="d-flex align-items-center gap-2">
      <?php if ($isView || $isEdit) : ?>
        <div class="btn-group">
          <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">📄 Generate</button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><?= Html::a('🖨️ Print', ['quotation/print-view', 'quotation_id' => $model->quotation_id], ['class' => 'dropdown-item', 'target' => '_blank']) ?></li>
            <li><?= Html::a('📧 Send Manually', ['quotation/prepare-email', 'quotation_id' => $model->quotation_id], ['class' => 'dropdown-item', 'target' => '_blank']) ?></li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li><?= Html::button('🤖 Send via Server', ['class' => 'dropdown-item', 'id' => 'btn-send-email-ajax', 'data-id' => $model->quotation_id, 'data-url' => Url::to(['/sales/quotation/send-email-with-attachment'])]) ?></li>
          </ul>
        </div>
      <?php endif; ?>

      <!-- --- PERUBAHAN TOMBOL DI SINI --- -->
      <?php if ($isView) : ?>
        <?= Html::button('Edit', ['class' => 'btn btn-warning', 'id' => 'btn-edit']) ?>
        <?= Html::submitButton('Save', ['class' => 'btn btn-custom-primary', 'id' => 'btn-save', 'style' => 'display:none;']) ?>
      <?php else: ?>
        <?= Html::submitButton('Submit', ['class' => 'btn btn-custom-primary', 'id' => 'btn-save']) ?>
      <?php endif; ?>
      <?= Html::button('Back', ['class' => 'btn btn-custom-secondary', 'onclick' => '$("#modal").modal("hide");']) ?>
    </div>
  </div>

  <?php ActiveForm::end(); ?>
</div>

<?php
JSRegister::begin(['position' => View::POS_END]);
?>

<script>
  $(document).ready(function() {
    const isCreate = <?= json_encode($isCreate) ?>;

    // --- Event handler untuk tombol delete ---
    $(document).on('click', '#delete-quotation-btn', function() {
      const id = $(this).data('id');
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
        if (result.isConfirmed) {
          $.ajax({
            url: '<?= Url::to(['/sales/quotation/delete']) ?>?quotation_id=' + id,
            type: 'POST',
            dataType: 'json',
            success: function(res) {
              if (res.status === 'success') {
                Swal.fire('Deleted!', res.message, 'success');
                $('#modal').modal('hide');
                $.pjax.reload({
                  container: "#gridDataquotation"
                });
              } else {
                Swal.fire('Error!', res.message, 'error');
              }
            },
            error: function() {
              Swal.fire('Error!', 'An error occurred while deleting data.', 'error');
            }
          });
        }
      });
    });

    // --- JAVASCRIPT UNTUK TABS ---
    $('.product-tab').on('click', function() {
      var tab = $(this).data('tab');
      $('.product-tab').removeClass('active');
      $(this).addClass('active');
      $('.product-fields-container').removeClass('active');
      $('#' + tab + '-fields').addClass('active');
    });

    // --- JAVASCRIPT UNTUK FIELD DINAMIS DAN FORMAT MATA UANG ---
    function formatRupiah(angka) {
      if (angka === null || isNaN(parseFloat(angka))) {
        return '';
      }
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
      if (typeof rupiahStr !== 'string' || !rupiahStr) {
        return 0;
      }
      // Hapus semua karakter kecuali digit, koma, dan minus
      let cleanStr = rupiahStr.replace(/[^\d,\-]/g, '');
      // Ganti semua koma dengan titik
      cleanStr = cleanStr.replace(/,/g, '.');
      // Ambil bagian sebelum titik desimal terakhir jika ada banyak titik
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
      $(document).on('keyup', selector, function() {
        $(this).val(formatRupiah($(this).val()));
      });
    }
    initializeCurrencyInput('.currency-input');

    function setInitialCustomerEmail() {
      const id = $('#customer_id').val();
      if (id) {
        $.getJSON('<?= Url::to(['/sales/quotation/get-info']) ?>', {
          id: id
        }, d => $('#customer_email').val(d.email));
      }
    }
    setInitialCustomerEmail();

    $('#customer_id').on('change', function() {
      $('#customer_email').val('');
      setInitialCustomerEmail();
    });

    function calculateTotal(prefix) {
      const priceVal = unformatRupiah($('#' + prefix + 'price_product').val());
      const unitVal = parseFloat($('#' + prefix + 'unit_product').val()) || 0;
      $('#' + prefix + 'total').val(formatRupiah(priceVal * unitVal));
    }

    $('#product_id').on('change', function() {
      const id = $(this).val();
      $('#unit_product, #price_product, #total').val('');
      if (id) {
        $.getJSON('<?= Url::to(['/sales/quotation/get-product-info']) ?>', {
          id: id
        }, d => {
          $('#unit_product').val(d.unit || 1);
          $('#price_product').val(formatRupiah(d.price || 0));
          calculateTotal('');
        });
      }
    });
    $('#unit_product, #price_product').on('input', () => calculateTotal(''));

    $('#optional_product_id').on('change', function() {
      const id = $(this).val();
      $('#optional_unit_product, #optional_price_product, #optional_total').val('');
      if (id) {
        $.getJSON('<?= Url::to(['/sales/quotation/get-product-info']) ?>', {
          id: id
        }, d => {
          $('#optional_unit_product').val(d.unit || 1);
          $('#optional_price_product').val(formatRupiah(d.price || 0));
          calculateTotal('optional_');
        });
      }
    });
    $('#optional_unit_product, #optional_price_product').on('input', () => calculateTotal('optional_'));

    $('#btn-edit').on('click', function() {
      $('#quotation-form').find('input, select, textarea').not('[readonly]').prop('disabled', false);
      $('#btn-save').show();
      $(this).hide();
    });

    // --- Submit form (Save/Edit) ---
    $(document).on('beforeSubmit', '#quotation-form', function(e) {
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
              if (response.success) {
                $('#modal').modal('hide');
                $.pjax.reload({
                  container: "#gridDataquotation"
                });
                Swal.fire('Success!', response.message, 'success');
              } else {
                let errorMsg = response.message || 'An error occurred.';
                if (response.errors) {
                  errorMsg += '\n';
                  $.each(response.errors, function(key, value) {
                    errorMsg += `- ${value.join(', ')}\n`;
                  });
                }
                Swal.fire('Error!', errorMsg, 'error');
              }
            },
            error: function() {
              Swal.fire('Error!', 'An error occurred while sending data.', 'error');
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

    // ... (kode untuk kirim email tidak berubah) ...
    $(document).on('click', '#btn-send-email-ajax', function() {
      const btn = $(this);
      const url = btn.data('url');
      const id = btn.data('id');
      Swal.fire({
        title: 'Send Email?',
        text: 'This will send the quotation to the customer and update the status to "Sent".',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Yes, send it!',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#27465E',
        cancelButtonColor: '#6c757d',
      }).then((result) => {
        if (result.isConfirmed) {
          btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Sending...');
          $.ajax({
            url: url,
            type: 'POST',
            data: {
              quotation_id: id
            },
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                Swal.fire('Sent!', response.message, 'success');
                $('#modalContent').load('<?= Url::to(['/sales/quotation/view']) ?>?quotation_id=' + id);
              } else {
                Swal.fire('Error!', response.message, 'error');
              }
            },
            error: function() {
              Swal.fire('Error!', 'An error occurred on the server.', 'error');
            },
            complete: function() {
              btn.prop('disabled', false).html('🤖 Send via Server (Automatic)');
            }
          });
        }
      });
    });
  });
</script>
<?php JSRegister::end(); ?>