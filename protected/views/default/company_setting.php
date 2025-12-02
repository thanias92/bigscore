<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
?>
<!-- OFFCANVAS STYLES -->
<style>
    .offcanvas-end {
        width: 450px !important;
        background-color: #f8fafc;
        box-shadow: -4px 0 10px rgba(0, 0, 0, 0.05);
        border-left: 1px solid #e2e8f0;
    }

    .offcanvas-title {
        font-weight: 600;
        color: #334155;
    }

    .form-label {
        font-weight: 500;
        color: #475569;
    }

    h6.text-primary {
        color: #0d6efd;
        font-size: 14px;
        margin-top: 1rem;
        margin-bottom: .5rem;
    }

    .btn-close {
        background: none;
        border: none;
    }

    input.form-control {
        border-radius: 8px;
        font-size: 14px;
    }

    .offcanvas-body {
        padding: 1rem 1.5rem;
    }
</style>

<!-- OFFCANVAS COMPONENT -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasSetting">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title">Company Setting</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>

  <div class="offcanvas-body small">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

        <h6 class="text-primary">Company Info</h6>
        <?php if ($model->pemasukan): ?>
            <div class="mb-3">
                <?= Html::activeLabel($model->pemasukan, 'pengirim_nama', ['class' => 'form-label']) ?>
                <?= Html::activeTextInput($model->pemasukan, 'pengirim_nama', [
                    'class' => 'form-control',
                    'readonly' => true
                ]) ?>
            </div>
            <div class="mb-3">
                <?= Html::activeLabel($model->pemasukan, 'pengirim_email', ['class' => 'form-label']) ?>
                <?= Html::activeTextInput($model->pemasukan, 'pengirim_email', [
                    'class' => 'form-control',
                    'readonly' => true
                ]) ?>
            </div>
        <?php endif; ?>

        <h6 class="text-primary">Logo</h6>
        <?= $form->field($model, 'logoFile')->fileInput()->label(false) ?>

        <h6 class="text-primary">Tanda Tangan Digital</h6>
        <canvas id="sig-pad" style="border:1px solid #ced4da;width:100%;height:150px"></canvas>
        <?= Html::hiddenInput('CompanySetting[signatureData]', '', ['id' => 'signatureData']) ?>
        <div class="mt-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" id="sig-clear">Clear</button>
        </div>

        <div class="mt-4 d-flex gap-2">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary flex-grow-1']) ?>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">Back</button>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

<!-- SignaturePad Script -->
<?php
$js = <<<JS
const canvas = document.getElementById('sig-pad');
const sigPad = new SignaturePad(canvas);
document.getElementById('sig-clear').onclick = () => sigPad.clear();

$('form').on('beforeSubmit', function() {
    if (!sigPad.isEmpty()) {
        $('#signatureData').val(sigPad.toDataURL());
    }
});
// $(document).on('click', '#openCompanySetting', function (e) {
//     e.preventDefault();
//     $.get('/default/setting-account', function (html) {
//       // hapus off‑canvas lama kalau ada
//       $('#company-setting-offcanvas-container').html(html);

//       // inisialisasi Bootstrap Offcanvas
//       let canvas = document.getElementById('offcanvasSetting');
//       let bsOffcanvas = bootstrap.Offcanvas.getOrCreateInstance(canvas);
//       bsOffcanvas.show();

//       // inisialisasi SignaturePad (jika ada)
//       let sigCanvas = document.getElementById('sig-pad');
//       if (sigCanvas) {
//         let pad = new SignaturePad(sigCanvas);
//         $('#sig-clear').on('click', () => pad.clear());

//         // sisipkan dataBase64 sebelum submit
//         $('form').on('beforeSubmit', function () {
//           if (!pad.isEmpty()) {
//             $('#signatureData').val(pad.toDataURL());
//           }
//         });
//       }
//     }).fail(() => alert('Gagal memuat pengaturan akun.'));
//   });

JS;
$this->registerJs($js);
?>
