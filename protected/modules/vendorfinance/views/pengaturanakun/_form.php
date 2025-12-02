<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
use yii\web\View;
use app\widgets\JSRegister;
use yii\helpers\Url;
?>

<div class="pengaturanakun-form">
  <?php $form = ActiveForm::begin([
    'id' => 'form',
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'options' => ['enctype' => 'multipart/form-data'],
    'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL]
  ]); ?>

  <div class="row g-4 align-items-stretch">
    <!-- LOGO -->
    <div class="col-md-6">
      <div class="card p-3 border rounded shadow-sm h-100">
        <h6 class="mb-2">Upload Logo</h6>
        <?= $form->field($model, 'logo', ['horizontalCssClasses' => ['wrapper' => 'col']])
          ->fileInput(['accept' => 'image/*'])
          ->label(false) ?>
        <?php if ($model->logo && file_exists(Yii::getAlias('@webroot/uploads/logo/' . $model->logo))): ?>
          <div class="text-center mt-2">
            <small class="text-muted d-block mb-1">Logo Sebelumnya:</small>
            <?= Html::img('@web/uploads/logo/' . $model->logo, ['style' => 'max-height:180px', 'class' => 'img-thumbnail']) ?>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- TTD CANVAS -->
    <div class="col-md-6">
      <div class="card p-3 border rounded shadow-sm h-100">
        <h6 class="mb-2">Tanda Tangan (Canvas)</h6>
        <canvas id="ttd-canvas" width="400" height="150"
          style="border:1px solid #ccc; background:#fff; border-radius:5px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);"></canvas>
        <?= Html::hiddenInput('ttd_data', '', ['id' => 'ttd_data']) ?>
        <div class="mt-2">
          <button type="button" class="btn btn-warning btn-sm" id="clear-canvas">Hapus</button>
        </div>
        <?php if ($model->ttd && file_exists(Yii::getAlias('@webroot/uploads/ttd/' . $model->ttd))): ?>
          <div class="text-center mt-2">
            <small class="text-muted d-block mb-1">Tanda Tangan Sebelumnya:</small>
            <?= Html::img('@web/uploads/ttd/' . $model->ttd, ['style' => 'max-height:80px', 'class' => 'img-thumbnail']) ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>


  <hr>
  <div class="d-flex justify-content-end mt-4">
    <?= Html::submitButton('<i class="fa fa-save"></i> Simpan', ['class' => 'btn btn-primary', 'id' => 'btn-save']) ?>
  </div>

  <?php ActiveForm::end(); ?>
</div>

<?php
JSRegister::begin(['position' => View::POS_END]);
?>
<script>
  $(function () {
    const canvas = document.getElementById('ttd-canvas');
    const ctx = canvas.getContext('2d');
    let drawing = false;

    // ✅ Fix ukuran pixel internal agar sesuai tampilan
    canvas.width = canvas.offsetWidth;
    canvas.height = canvas.offsetHeight;

    // Mulai menggambar
    canvas.addEventListener('mousedown', e => {
      drawing = true;
      ctx.beginPath();
      ctx.moveTo(e.offsetX, e.offsetY);
    });

    // Saat mouse bergerak
    canvas.addEventListener('mousemove', e => {
      if (!drawing) return;
      ctx.lineWidth = 2;
      ctx.lineCap = 'round';
      ctx.strokeStyle = '#000';
      ctx.lineTo(e.offsetX, e.offsetY);
      ctx.stroke();
    });

    // Selesai menggambar
    canvas.addEventListener('mouseup', () => drawing = false);
    canvas.addEventListener('mouseleave', () => drawing = false);

    $('#clear-canvas').click(() => {
      ctx.clearRect(0, 0, canvas.width, canvas.height);
    });

    /* ---- AJAX submit ---- */
    $('#form').submit(e => e.preventDefault());

    $('#btn-save').click(() => {
      const form = $('#form')[0];
      const fd = new FormData(form);
      fd.append('ttd_data', canvas.toDataURL('image/png'));

      Swal.fire({
        title: 'Konfirmasi',
        text: 'Simpan perubahan?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Lanjutkan',
        cancelButtonText: 'Batal'
      }).then(result => {
        if (!result.isConfirmed) return;

        $.ajax({
          type: 'POST',
          url: $('#form').attr('action'),
          data: fd,
          processData: false,
          contentType: false,
          success: r => {
            const resp = typeof r === 'string' ? JSON.parse(r) : r;
            if (resp.status === 'success') {
              Swal.fire({
                title: 'Berhasil',
                text: resp.message,
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
              }).then(() => {
                $('#modal').modal('hide');
                if (resp.redirect) {
                  window.location.href = resp.redirect;
                } else {
                  location.reload(); // fallback jika redirect tidak tersedia
                }
              });
            } else {
              Swal.fire('Gagal', resp.message, 'warning');
            }
          },
          error: () => {
            Swal.fire('Gagal', 'Terjadi kesalahan koneksi.', 'error');
          }
        });
      });
    });

  });
</script>
<?php JSRegister::end(); ?>