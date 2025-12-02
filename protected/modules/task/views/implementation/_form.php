<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
use yii\web\View;
use app\widgets\JSRegister;
    //kanza push ulang untuk hosting
    ?>

<div class="implementation-form">

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

  <style>
    .border-input {
      border: 1px solid #ccc !important;
    }

    .bg-button-danger {
      background: red;
      color: white;
    }
  </style>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

  <div class="row flex flex-col gap-2">

    <div class="col-md-12">
      <div class="row">
        <div class="col-md-6">
          <label for="">Judul Aktivitas</label>
          <input type="text" id="judul" placeholder="Judul Aktivitas"
            class="w-full p-2 bg-white rounded-lg border-1 border-input"
            name="judul"
            value="<?= Html::encode(@$model->activity_title ?? '') ?>">
          <div class="invalid-feedback"></div>
        </div>

        <div class="col-md-6">
          <label for="">Status</label>
          <select
            class="w-full p-2 bg-white rounded-lg border-1 border-input"
            name="status" id="status">
            <option value=""></option>
            <?php foreach ($statusProgress as $val): ?>
              <option value="<?= htmlspecialchars($val) ?>" <?= @$model->status == $val ? 'selected' : '' ?>>
                <?= htmlspecialchars($val) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <div class="invalid-feedback"></div>
        </div>
      </div>
    </div>


    <div id="detail-wrapper">
      <?php if (!empty($detailList)): ?>
        <?php foreach ($detailList as $index => $item): ?>
          <div class="detail-group bg-gray-100 rounded-lg p-3 mt-3">
            <div class="col-md-12 mb-2">
              <label>Aktivitas</label>
              <input type="text" placeholder="Aktivitas" class="w-full p-2 bg-white rounded-lg border-1 border-input" name="detail[<?= $index ?>][aktivitas]" value="<?= Html::encode($item['activity']) ?>">
            </div>

            <div class="col-md-12">
              <label for="">Detail</label>
              <input type="text" placeholder="Detail" class="w-full p-2 bg-white rounded-lg border-1 border-input" name="detail[<?= $index ?>][detail]" value="<?= Html::encode($item['detail']) ?>">
            </div>

            <div class="col-md-12">
              <label for="">PIC</label>
              <input type="text" placeholder="PIC" class="w-full p-2 bg-white rounded-lg border-1 border-input" name="detail[<?= $index ?>][pic]" value="<?= Html::encode($item['pic_aktivitas']) ?>">
            </div>

            <div class="col-md-12">
              <div class="row">
                <div class="col-md-6">
                  <label for="">Start Date</label>
                  <input type="date" placeholder="Start Date" class="w-full p-2 bg-white rounded-lg border-1 border-input" name="detail[<?= $index ?>][start_date]" value="<?= Html::encode($item['start_date']) ?>">
                </div>
                <div class="col-md-6">
                  <label for="">Completion Date</label>
                  <input type="date" placeholder="End Date" class="w-full p-2 bg-white rounded-lg border-1 border-input" name="detail[<?= $index ?>][end_date]" value="<?= Html::encode($item['completion_date']) ?>">
                </div>
              </div>
            </div>

            <div class="col-md-12">
              <label for="">Catatan</label>
              <textarea class="w-full p-2 bg-white rounded-lg border-1 border-input" name="detail[<?= $index ?>][catatan]"><?= Html::encode($item['notes']) ?></textarea>
            </div>
            <?php if ($index > 0): ?>
              <div class="col-md-12 flex justify-end mt-2">
                <button type="button" class="btn btn-danger btn-hapus">Hapus</button>
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="detail-group bg-gray-100 rounded-lg p-3 mt-3">
          <div class="col-md-12">
            <label for="">Aktivitas</label>
            <input type="text" placeholder="Aktivitas" class="w-full p-2 bg-white rounded-lg border-1 border-input" name="detail[0][aktivitas]">
          </div>
          <div class="col-md-12">
            <label for="">Detail</label>
            <input type="text" placeholder="Detail" class="w-full p-2 bg-white rounded-lg border-1 border-input" name="detail[0][detail]">
          </div>
          <div class="col-md-12">
            <label for="">PIC</label>
            <input type="text" placeholder="PIC" class="w-full p-2 bg-white rounded-lg border-1 border-input" name="detail[0][pic]">
          </div>
          <div class="col-md-12">
            <div class="row">
              <div class="col-md-6">
                <label for="">Start Date</label>
                <input type="date" placeholder="Start Date" class="w-full p-2 bg-white rounded-lg border-1 border-input" name="detail[0][start_date]">
              </div>
              <div class="col-md-6">
                <label for="">Completion Date</label>
                <input type="date" placeholder="End Date" class="w-full p-2 bg-white rounded-lg border-1 border-input" name="detail[0][end_date]">
              </div>
            </div>
          </div>
          <div class="col-md-12">
            <label for="">Catatan</label>
            <textarea class="w-full p-2 bg-white rounded-lg border-1 border-input" name="detail[0][catatan]"></textarea>
          </div>
        </div>
      <?php endif; ?>
    </div>

    <div class="col-md-12 flex justify-end mt-3">
      <button id="tambah" type="button" class="flex flex-row justify-center items-center rounded-lg bg-button px-4 py-2 text-sm">+Tambah</button>
    </div>


    <hr />
    <div class="form-group d-flex justify-content-between">
      <?= Html::button('Back', ['class' => 'btn btn-secondary btn-sm', 'onclick' => '$("#modal").modal("hide");']) ?>
      <?= Html::button('<i class="fa fa-save"></i> Simpan', ['class' => 'btn btn-primary btn-sm', 'id' => 'btn-save']) ?>

    </div>

    <?php ActiveForm::end(); ?>

  </div>


  <?php
  JSRegister::begin(['position' => View::POS_END]);
  ?>
  <script>

    
    // $('#form').submit(function(e) {
    //   e.preventDefault();
    // });

    // $(document).on("click", "#btn-save", function() {
    //   var form = $('#form')[0];
    //   var link_simpan = $('#form').attr('action');
    //   var formData = new FormData(form); // Gunakan FormData, bukan serialize

    //   Swal.fire({
    //     title: 'Konfirmasi',
    //     text: 'Apakah Akan Menyimpan Data?',
    //     showCancelButton: true,
    //     confirmButtonText: 'Lanjutkan',
    //     cancelButtonText: 'Batal',
    //     icon: 'question',
    //   }).then((result) => {
    //     if (result.isConfirmed) {
    //       $.ajax({
    //         type: "POST",
    //         url: link_simpan,
    //         data: formData,
    //         contentType: false,
    //         processData: false,
    //         success: function(response) {
    //           if (response.status === 'success') {
    //             $('#modal').modal('hide');
    //             Swal.fire('OK', response.message, 'success').then(() => {
    //               location.reload();
    //             });
    //           } else {
    //             Swal.fire('Terjadi Kesalahan.', response.message, 'warning');
    //           }
    //         },
    //         error: function(xhr, status, error) {
    //           console.error(xhr.responseText);
    //           Swal.fire('Error', 'Gagal menyimpan data.', 'error');
    //         }
    //       });
    //     }
    //   });



    // });

    $('#btn-save').click(function(e) {
      e.preventDefault();

      var form = $('#form')[0];
      var link_simpan = $('#form').attr('action');
      var formData = new FormData(form);

      $.ajax({
        type: "POST",
        url: link_simpan,
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
          if (response.status === 'success') {
            $('#modal').modal('hide');
            Swal.fire('OK', response.message, 'success').then(() => {
              location.reload();
            });
          } else {
            Swal.fire('Terjadi Kesalahan.', response.message, 'warning');
          }
        },
        error: function(xhr, status, error) {
          console.error(xhr.responseText);
          Swal.fire('Error', 'Gagal menyimpan data.', 'error');
        }
      });
    });



  </script>
  <?php JSRegister::end(); ?>

  <script>
    <?php if (!empty($detailList)): ?>
      var detailIndexd = <?= count($detailList) ?>;
    <?php else: ?>
      var detailIndexd = 1;
    <?php endif; ?>
    $('#tambah').on('click', function(e) {
      e.preventDefault();
      let newDetail = $('.detail-group').first().clone();
      newDetail.find('input, textarea').val('');
      newDetail.find('input, textarea').each(function() {
        let name = $(this).attr('name');
        if (name) {
          let newName = name.replace(/\[\d+\]/, '[' + detailIndexd + ']');
          $(this).attr('name', newName);
        }
      });
      newDetail.append(`
                <div class="col-md-12 flex justify-end mt-2">
                    <button type="button" class="flex flex-row justify-center items-center rounded-lg bg-button-danger px-4 py-2 text-sm btn-danger btn-hapus">Hapus</button>
                </div>
            `);

      $('#detail-wrapper').append(newDetail);
      detailIndexd++;
    });
    $('#detail-wrapper').on('click', '.btn-hapus', function() {
      $(this).closest('.detail-group').remove();
    });
  </script>