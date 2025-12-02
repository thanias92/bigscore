<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\web\View;
use app\widgets\JSRegister;

/* @var $model app\models\Pemasukan */
/* @var $akunPemasukanList app\models\Accountkeluar[] */
?>

<style>
  .help-block {
    color: red !important
  }
</style>

<div class="pemasukan-form">
  <?php $form = ActiveForm::begin([
    'id' => 'form-pemasukan',
    'enableClientValidation' => true,
  ]); ?>

  <!-- Pengirim/Penerima -->
  <div class="row mb-4">
    <div class="col-md-6">
      <h5>Sender</h5>
      <?= $form->field($model, 'pengirim_nama')
        ->textInput(['value' => 'PT. Bigs Integrasi Teknologi', 'readonly' => true]) ?>
      <?= $form->field($model, 'pengirim_email')
        ->textInput(['value' => 'info@bigsgroup.co.id', 'readonly' => true]) ?>
    </div>

    <div class="col-md-6">
      <h5>Receiver</h5>
      <?= $form->field($model, 'deals_id')->widget(Select2::class, [
        'data' => \yii\helpers\ArrayHelper::map(
          \app\models\Deals::find()->with('customer')->all(),
          'deals_id',
          fn($m) => $m->customer ? $m->customer->customer_name : 'Tanpa Customer'
        ),
        'options' => ['placeholder' => 'Select Customer', 'id' => 'deals-id'],
        'pluginOptions' => ['allowClear' => true, 'dropdownParent' => new \yii\web\JsExpression("$('#modal')")],
      ])->label('Customer <span class="text-danger">*</span>', ['encode' => false]) ?>

     
      <div class="form-group">
        <!-- <label>Email Recipient</label> -->
        <?= $form->field($model, 'penerima_email')
        ->textInput(['readonly' => true, 'id' => 'penerima-email'])
        ->label('Email Recipient') ?>
      </div>
    </div>
  </div>

  <!-- Jenis & Cicilan -->
  <div class="row mb-4">
    <div class="col-md-6">
      <?= $form->field($model, 'purchase_type')
        ->textInput(['readonly' => true, 'id' => 'purchase-type']) ?>
    </div>

    <div class="col-md-6">
      <?= $form->field($model, 'cicilan')->dropDownList([
        0 => 'Without Installments',
        3 => ' 3 Installment',
        6 => ' 6 Installment',
      ], [
        'id' => 'pemasukan-cicilan',
        'value' => $model->cicilan ?? 0,
      ]) ?>
      <div id="simulasi-cicilan" class="mt-2"></div> <!-- ✅ Preview Cicilan -->
    </div>
  </div>

  <!-- Tanggal -->
  <div class="row mb-6">
    <div class="col-md-6">
      <?= $form->field($model, 'purchase_date')->widget(DatePicker::class, [
        'options' => ['autocomplete' => 'off', 'id' => 'purchase-date'],
        'pluginOptions' => ['autoclose' => true, 'format' => 'dd/mm/yyyy', 'todayHighlight' => true],
      ]) ?>
    </div>
    <div class="col-md-6">
      <?= $form->field($model, 'tgl_jatuhtempo')->widget(DatePicker::class, [
        'options' => ['autocomplete' => 'off', 'id' => 'tgl-jatuhtempo'],
        'pluginOptions' => ['autoclose' => true, 'format' => 'dd/mm/yyyy', 'todayHighlight' => true],
      ]) ?>
    </div>
  </div>

  <!-- Tabel Produk -->
  <div class="table-responsive mb-4">
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Product</th>
          <th>Unit</th>
          <th>PPN</th>
          <th>Product Price</th>
          <th>Total</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><?= Html::activeTextInput($model, 'produk', ['class' => 'form-control', 'readonly' => true, 'id' => 'product-name']) ?></td>
          <td><?= Html::activeTextInput($model, 'unit', ['class' => 'form-control', 'id' => 'unit']) ?></td>
          <td><?= Html::textInput('ppn_disp', 'PPN [11%]', ['class' => 'form-control', 'readonly' => true]) ?></td>
          <td>
            <?= Html::activeHiddenInput($model, 'price_product', ['id' => 'price-product']) ?>
            <span id="price-product-display" class="form-control bg-light" readonly></span>
          </td>
          <td>
            <?= Html::hiddenInput('Pemasukan[total]', $model->deals ? $model->deals->total : 0, ['id' => 'pemasukan-total']) ?>
            <span id="total-display" class="form-control bg-light" readonly></span>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- Ringkasan Total -->
  <div class="row justify-content-end mb-4">
    <div class="col-md-4">
      <table class="table table-borderless">
        <tr>
          <th>Sub Total</th>
          <td>
            <?= Html::activeHiddenInput($model, 'sub_total', ['id' => 'pemasukan-sub_total']) ?>
            <span id="subtotal-display" class="form-control bg-light" readonly></span>
          </td>
        </tr>
        <tr>
          <th>Pajak</th>
          <td><?= Html::textInput('ppn', 'PPN [11%]', ['class' => 'form-control', 'readonly' => true]) ?></td>
        </tr>
        <tr>
          <th>Diskon</th>
          <td>
            <div class="input-group">
              <?= Html::activeTextInput($model, 'diskon', [
                'class' => 'form-control',
                'id' => 'diskon',
                'type' => 'number',
                'min' => 0,
                'max' => 100,
                'step' => 1,
              ]) ?>
              <span class="input-group-text">%</span>
            </div>
          </td>
        </tr>
        <tr>
          <th><strong>Total</strong></th>
          <td>
            <?= Html::activeHiddenInput($model, 'grand_total', ['id' => 'pemasukan-grand_total']) ?>
            <span id="grand-total-display" class="form-control bg-light" readonly></span>
          </td>
        </tr>
        <?= Html::activeHiddenInput($model, 'sisa_tagihan', ['id' => 'pemasukan-sisa_tagihan']) ?>
      </table>
    </div>
  </div>

  <!-- Keterangan -->
  <?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>

  <!-- Tombol -->
  <div class="form-group text-end">
    <?= Html::button(
      '<i class="fa fa-save"></i> Simpan',
      ['class' => 'btn btn-success', 'id' => 'btn-save']
    ) ?>
  </div>

  <?php ActiveForm::end(); ?>
</div>

<?php
JSRegister::begin(['position' => View::POS_END]); ?>
<script>
  $(function() {

    if ($('#deals-id').val()) $('#deals-id').trigger('change');

    const rupiah = n => new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR'
    }).format(n);

    $('#purchase-date').on('change', function() {
      const [d, m, y] = this.value.split('/');
      if (!y) return;
      const due = new Date(y, m - 1, d);
      due.setDate(due.getDate() + 15);
      $('#tgl-jatuhtempo').val(('0' + due.getDate()).slice(-2) + '/' + ('0' + (due.getMonth() + 1)).slice(-2) + '/' + due.getFullYear());
    });

    function refreshCicilanUI() {
      const unit = parseInt($('#unit').val() || 0, 10);
      const pt = $('#purchase-type').val();
      const dd = $('#pemasukan-cicilan');

      const allowed = (pt === 'Outright Purchase - Installments' && unit >= 12);
      dd.prop('disabled', !allowed);
      if (!allowed) {
        dd.val(0).trigger('change');
      }
    }

    $('#unit,#purchase-type').on('input change', refreshCicilanUI);
    refreshCicilanUI();

    function hitung() {
      const price = parseFloat($('#price-product').val()) || 0;
      const unit = parseInt($('#unit').val()) || 0;
      const sub = price * unit;
      const ppn = sub * 0.11;
      const discP = parseFloat($('#diskon').val()) || 0;
      const grand = sub + ppn - (sub * discP / 100);

      $('#price-product-display').text(rupiah(price));
      $('#total-display').text(rupiah(sub));
      $('#subtotal-display').text(rupiah(sub));
      $('#grand-total-display').text(rupiah(grand));

      $('#pemasukan-total').val(sub);
      $('#pemasukan-sub_total').val(Math.round(sub));
      $('#pemasukan-grand_total').val(Math.round(grand));
      $('#pemasukan-sisa_tagihan').val(Math.round(grand));
      renderSimulasiCicilan(); // ✅ panggil saat hitung
    }

    function renderSimulasiCicilan() {
      const cicil = parseInt($('#pemasukan-cicilan').val()) || 0;
      const grand = parseFloat($('#pemasukan-grand_total').val()) || 0;
      const output = $('#simulasi-cicilan');

      if (cicil > 0 && grand > 0) {
        const nominalDasar = Math.floor(grand / cicil);
        const selisih = grand - (nominalDasar * cicil);
        let html = '<div class="border p-2 rounded bg-light"><strong>Installment Simulation:</strong><ul class="mb-0">';
        for (let i = 1; i <= cicil; i++) {
          let nominal = nominalDasar;
          if (i === cicil) nominal += selisih;
          html += `<li>Installment ke-${i}: <strong>${rupiah(nominal)}</strong></li>`;
        }
        html += '</ul></div>';
        output.html(html);
      } else {
        output.empty();
      }
    }

    $('#unit,#price-product,#diskon,#pemasukan-cicilan').on('input change', function() {
      hitung();
    });

    $('#deals-id').on('change', function() {
      const id = $(this).val();
      if (!id) return;
      $.get('/vendorfinance/pemasukan/get-deals-data', {
        id
      }, res => {
        if (res.error) {
          alert(res.error);
          return;
        }
        $('#product-name').val(res.product_name);
        $('#unit').val(res.unit).trigger('input');
        $('#price-product').val(res.price_product);
        $('#pemasukan-total').val(res.total);
        $('#penerima-email').val(res.customer_email);
        $('#purchase-type').val(res.purchase_type).trigger('change');
        hitung();
      });
    });

    function cekCicilan() {
      const cicil = parseInt($('#pemasukan-cicilan').val() || 0, 10);
      const pt = $('#purchase-type').val();
      if (cicil > 0 && pt !== 'Outright Purchase - Installments') {
        Swal.fire('Cicilan tidak berlaku', 'Hanya boleh untuk “Outright Purchase – Installments”.', 'warning');
        $('#pemasukan-cicilan').val(0);
        renderSimulasiCicilan();
      }
    }

    $('#pemasukan-cicilan,#purchase-type').on('change', cekCicilan);

    $('#btn-save').on('click', function(e) {
      e.preventDefault();
      hitung();
      Swal.fire({
          title: 'Simpan data?',
          icon: 'question',
          showCancelButton: true
        })
        .then(r => {
          if (!r.isConfirmed) return;
          $.post($('#form-pemasukan').attr('action'),
              $('#form-pemasukan').serialize())
            .done(res => {
              if (res.status === 'success') {
                $('#modal').modal('hide');
                Swal.fire('OK', res.message, 'success');
                $.pjax.reload({
                  container: '#gridDatapemasukan'
                });
              } else {
                Swal.fire('Gagal', res.message, 'error');
                console.log(res.errors);
              }
            })
            .fail(() => Swal.fire('Error', 'Server error', 'error'));
        });
    });

    hitung(); // Hitung awal
  });
</script>
<?php JSRegister::end(); ?>