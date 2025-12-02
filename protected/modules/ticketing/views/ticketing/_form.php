<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\Url;
use yii\web\View;
use app\widgets\JSRegister;

/* @var $this yii\web\View */
/* @var $model app\models\Ticket */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ticket-form">
    <?php $form = ActiveForm::begin([
        'id' => 'form',
        'type' => ActiveForm::TYPE_VERTICAL,
        'formConfig' => ['deviceSize' => ActiveForm::SIZE_SMALL],
    ]); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'code_ticket')->textInput([
                'maxlength' => true,
                'class' => 'form-control-sm'
            ]) ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'id_deals')->widget(Select2::class, [
                'data' => \yii\helpers\ArrayHelper::map($deals, 'deals_id', fn($d) => $d->customer->customer_name ?? '(Tidak ada nama)'),
                'options' => ['placeholder' => 'Pilih Customer', 'id' => 'dealsr_id'],
                'pluginOptions' => [
                    'allowClear' => true,
                    'dropdownParent' => new \yii\web\JsExpression("$('#modal')"),
                ],
                'size' => Select2::SMALL,
            ]) ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'user')->textInput([
                'maxlength' => true,
                'class' => 'form-control-sm'
            ]) ?>
        </div>

        <div class="col-md-6">
            <?= Html::label('Email Customer', null, ['class' => 'form-label small']) ?>
            <input type="text" class="form-control form-control-sm" id="customer_email" readonly>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'date_ticket')->textInput([
                'type' => 'date',
                'class' => 'form-control-sm'
            ]) ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'duedate')->textInput([
                'type' => 'date',
                'class' => 'form-control-sm'
            ]) ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'priority_ticket')->dropDownList([
                'Low' => 'Low',
                'Medium' => 'Medium',
                'High' => 'High'
            ], ['prompt' => 'Pilih Prioritas', 'class' => 'form-control-sm']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'label_ticket')->textInput([
                'value' => 'Bug',
                'readonly' => true,
                'class' => 'form-control-sm'
            ]) ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'assigne')->dropDownList([
                'Pasman Rizky' => 'Pasman Rizky',
                'Nanang Sunardi' => 'Nanang Sunardi',
                'Iwan' => 'Iwan'
            ], ['prompt' => 'Pilih Assigne', 'class' => 'form-control-sm']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'via')->dropDownList([
                'Ticket Mandiri' => 'Ticket Mandiri',
                'Roomchat' => 'Roomchat',
                'Whatsapp' => 'WhatsApp'
            ], ['prompt' => 'Pilih Via', 'class' => 'form-control-sm']) ?>
        </div>

        <div class="col-md-12">
            <?= $form->field($model, 'title')->textInput([
                'maxlength' => true,
                'class' => 'form-control-sm'
            ]) ?>
        </div>
        <div class="col-md-12">
            <?= $form->field($model, 'modul')->textInput([
                'maxlength' => true,
                'class' => 'form-control-sm'
            ]) ?>
        </div>
        <div class="col-md-12">
            <?= $form->field($model, 'status_ticket')->textInput([
                'value' => 'Waiting',
                'readonly' => true,
                'class' => 'form-control-sm'
            ]) ?>
        </div>
        <div class="col-md-12">
            <?= $form->field($model, 'description')->textarea([
                'rows' => 4,
                'class' => 'form-control-sm'
            ]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'role')->hiddenInput(['value' => 'staff'])->label(false) ?>
        </div>
    </div>

    <hr />
    <div class="form-group d-flex justify-content-end gap-2 mt-3">
        <?= Html::submitButton('Submit', [
            'class' => 'btn btn-primary  btn-submit',
            'id' => 'btn-save'
        ]) ?>
        <?= Html::button('Back', [
            'class' => 'btn btn-secondary btn-back',
            'onclick' => '$("#modal").modal("hide");'
        ]) ?>

    </div>


    <?php ActiveForm::end(); ?>
</div>

<?php
$script = <<<JS
$('#dealsr_id').on('change', function() {
  var id = $(this).val();
  if(id){
    $.ajax({
      url: '/ticketing/ticketing/get-info',
      data: { id: id },
      success: function(data) {
        $('#customer_email').val(data.email || '');
      },
      error: function() {
        $('#customer_email').val('');
        alert('Gagal mengambil data customer.');
      }
    });
  } else {
    $('#customer_email').val('');
  }
});
JS;
$this->registerJs($script);
?>

<?php JSRegister::begin(['position' => View::POS_END]); ?>
<script>
    $('#form').submit(function(e) {
        e.preventDefault(); // Hindari submit default
    });

    $(document).on("click", "#btn-save", function() {
        const link_simpan = $('#form').attr('action');
        const data_form = $('#form').serializeArray();

        Swal.fire({
            title: 'Konfirmasi',
            text: 'Apakah Akan Menyimpan Data?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Lanjutkan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: link_simpan,
                    data: data_form,
                    dataType: "html",
                    success: function(response) {
                        const res = JSON.parse(response);
                        if (res.status === 'success') {
                            $('#modal').modal('hide');
                            Swal.fire('Berhasil', res.message, 'success');
                            $.pjax.reload({
                                container: "#gridDataticket"
                            });
                        } else {
                            Swal.fire('Gagal', res.message, 'warning');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Gagal mengirim data ke server.', 'error');
                    }
                });
            }
        });
    });
</script>
<?php JSRegister::end(); ?>