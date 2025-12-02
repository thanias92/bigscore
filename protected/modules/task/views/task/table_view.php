<?php
// protected/modules/task/views/task/table_view.php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\grid\ActionColumn;
use yii\web\View;
use app\widgets\JSRegister;
use kartik\grid\GridView;
use yii\bootstrap5\Modal;
use dosamigos\ckeditor\CKEditor;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 */
?>

<style>
  select {
    width: 120px;
    border-radius: 10px;
    border: 1px solid #ccc;
  }

  table {
    border: 1px solid #C0BFC0;
  }

  table th a {
    color: #3F4254 !important;
    text-decoration: none;
  }

  table th a:hover {
    color: #333 !important;
  }

  /* Hilangkan semua border dulu */
  .table-custom,
  .table-custom th,
  .table-custom td {
    border: none !important;
  }

  /* Tambahkan hanya border horizontal (bottom untuk setiap baris) */
  .table-custom tr td,
  .table-custom tr th {
    border-bottom: 1px solid #ccc !important;
    /* kamu bisa ganti warna/ketebalan */
  }

  /* Opsional: tambahkan border-top hanya untuk header */
  .table-custom thead tr th {
    border-top: 1px solid #ccc !important;
    background-color: #E5E7EB;
    /* warna abu untuk header */
    color: #3F4254;
  }

  .kv-grid-table,
  .kv-grid-table th,
  .kv-grid-table td {
    border: none !important;
  }

  .kv-grid-table tr td,
  .kv-grid-table tr th {
    border-bottom: 1px solid #ccc !important;
  }

  .kv-grid-table tbody tr:last-child td {
    border-bottom: none !important;
  }

  body {
    background-color: rgba(245, 248, 250, 1) !important;
  }
</style>

<style>
  /* Task Item Card */
  .task-item {
    background-color: #fff;
    border-radius: 10px;
    padding: 15px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease-in-out;
    display: flex;
    flex-direction: column;
  }

  /* Tanggal di kanan atas */
  .task-item .task-date {
    position: absolute;
    top: 10px;
    right: 10px;
    background-color: #ffffff;
    color: #333;
    padding: 5px 10px;
    border-radius: 5px;
    font-size: 0.8rem;
  }

  /* Bagian "details" di kanan bawah */
  .task-item .details {
    position: absolute;
    bottom: 10px;
    right: 10px;
    font-size: 0.9rem;
    color: #007bff;
    cursor: pointer;
  }

  .task-item:hover {
    transform: translateY(-5px);
  }

  .task-item h3 {
    margin-bottom: 10px;
    font-size: 1.2rem;
    font-weight: bold;
    color: #333;
  }

  .task-item .modul-name {
    font-size: 0.9rem;
    color: #666;
    margin-bottom: 10px;
  }

  .task-item .assigned-task {
    display: flex;
    align-items: center;
    font-size: 0.8rem;
    color: #333;
    margin-bottom: 10px;
  }

  .task-item .assigned-task img {
    border-radius: 20%;
    margin-right: 4px;
    width: 20px;
    height: 20px;
  }

  .task-item .priority,
  .task-item .label {
    font-size: 0.8rem;
    padding: 4px 8px;
    border-radius: 4px;
    display: inline-block;
    margin-top: 10px;
  }

  .task-item .priority,
  .task-item .label {
    margin-right: 10px;
  }

  .task-item .priority {
    display: inline-flex;
    align-items: center;
  }

  .task-item .priority.low {
    background-color: #d4edda;
    color: #28a745;
  }

  .task-item .priority.medium {
    background-color: #fff3cd;
    color: #ffc107;
  }

  .task-item .priority.high {
    background-color: #f8d7da;
    color: #dc3545;
  }

  .task-item .label {
    background-color: #e0f7ff;
    color: #007bff;
  }

  .task-item .priority,
  .task-item .label {
    max-width: auto;
    white-space: nowrap;
  }

  .btn.btn-primary:hover {
    background-color: #1f384d;
    border-color: #1f384d;
  }

  .btn.btn-primary {
    background-color: rgba(39, 70, 94, 1);
    color: rgba(243, 244, 247, 1);
    font-family: 'Inter Semi Bold', sans-serif;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 5px;
    cursor: pointer;
    text-decoration: none;
    margin-left: 10px;
  }

  /* Styling Tombol Back */
  .btn.btn-secondary.btn-sm {
    background-color: #D9D9D9;
    color: black;
    font-size: 1rem;
    padding: 8px 20px;
    border-radius: 5px;
    margin-left: auto;
    margin-right: 10px;
    text-align: center;
  }

  .btn.btn-secondary.btn-sm:hover {
    background-color: #D9D9D9;
    border-color: #D9D9D9;
  }

  .search-add-bar {
    display: flex;
    align-items: center;
    gap: 10px;
    justify-content: flex-end;
    /* Tetapkan ke kanan, nanti flex-grow atur */
  }

  .search-input-group {
    display: flex;
    border: 1px solid #ced4da;
    border-radius: 5px;
    overflow: hidden;
    height: 30px;
    width: 180px;
    /* Lebar tetap untuk search input */
    flex-shrink: 0;
    /* Agar tidak menyusut */
  }

  .search-input {
    border: none;
    padding: 15px 1px 2px 8px;
    font-size: 0.9rem;
    color: #495057;
    flex-grow: 1;
    /* Biarkan memanjang di dalamnya */
    height: 100%;
    padding-left: 10px;
    text-align: left;
    display: flex;
    /* Aktifkan Flexbox di dalam input */
    align-items: center;
    /* Tengahkan konten secara vertikal */
  }

  .search-input:focus {
    outline: none;
  }

  .search-input::placeholder {
    text-align: left;
    /* Tetap rata kiri untuk placeholder */
    color: #6c757d;
    display: flex;
    /* Aktifkan Flexbox untuk placeholder */
    align-items: center;
    /* Tengahkan placeholder secara vertikal */
    height: 100%;
    /* Pastikan placeholder mengisi tinggi input */
  }

  .search-button {
    background-color: transparent;
    border: none;
    padding: 4px 8px;
    color: #007bff;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
  }

  .form-select-sm {
    border: 1px solid #ced4da;
    border-radius: 5px;
    overflow: hidden;
    height: 30px;
    width: 180px;
    /* Lebar tetap untuk dropdown (sama dengan search) */
    background-color: #fff;
    padding: 4px 10px;
    font-size: 0.9rem;
    color: #495057;
    cursor: pointer;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    /* Agar tidak menyusut */
  }

  .form-select-sm:focus {
    outline: none;
    border-color: #ced4da;
    box-shadow: none;
  }

  .showModalButton.btn.btn-primary {
    background-color: #2c3e50;
    color: #fff;
    border: none;
    border-radius: 5px;
    padding: 4px 12px;
    font-size: 0.9rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 5px;
    height: 30px;
    width: auto;
  }

  .showModalButton.btn.btn-primary:hover {
    background-color: #1a252f;
  }

  /* Background container to avoid issues with card layout */
  .task-list-container {
    position: relative;
  }

  .btn.btn-link {
    color: rgb(17, 33, 56);
    text-decoration: none;
    font-weight: 600;
    position: relative;
  }

  .btn.btn-link:hover {
    color: rgb(17, 33, 56);
    text-decoration: none;
  }

  .btn.btn-link:hover::after {
    content: "";
    position: absolute;
    left: 0;
    bottom: -3px;
    width: 100%;
    height: 3px;
    background-color: rgb(17, 33, 56);
    border-radius: 2px;
  }

  .btn.btn-link.active {
    color: rgb(17, 33, 56);
    font-weight: 600;
    pointer-events: none;
    text-decoration: none;
    position: relative;
  }

  .btn.btn-link.active::after {
    content: "";
    position: absolute;
    left: 0;
    bottom: -3px;
    width: 100%;
    height: 3px;
    background-color: rgb(17, 33, 56);
    border-radius: 2px;
  }

  body {
    background-color: rgba(245, 248, 250, 1) !important;
  }


  table {
    border: 1px solid #C0BFC0;
  }

  table th a {
    color: #3F4254 !important;
    text-decoration: none;
  }

  table th a:hover {
    color: #333 !important;
  }

  /* Hilangkan semua border dulu */
  .table-custom,
  .table-custom th,
  .table-custom td {
    border: none !important;
  }

  /* Tambahkan hanya border horizontal (bottom untuk setiap baris) */
  .table-custom tr td,
  .table-custom tr th {
    border-bottom: 1px solid #ccc !important;
    /* kamu bisa ganti warna/ketebalan */
  }

  /* Opsional: tambahkan border-top hanya untuk header */
  .table-custom thead tr th {
    border-top: 1px solid #ccc !important;
    background-color: #E5E7EB;
    /* warna abu untuk header */
    color: #3F4254;
  }

  .kv-grid-table,
  .kv-grid-table th,
  .kv-grid-table td {
    border: none !important;
  }

  .kv-grid-table tr td,
  .kv-grid-table tr th {
    border-bottom: 1px solid #ccc !important;
  }

  .kv-grid-table tbody tr:last-child td {
    border-bottom: none !important;
  }
</style>

<?php Modal::begin([
  'headerOptions' => ['id' => 'modalHeader'],
  'id' => 'modal',
  'size' => 'modal-xl',
  'options' => [
    'data-bs-backdrop' => 'static',
    'data-bs-keyboard' => 'false',
    'tabindex' => false,
  ],
]);
echo "<div id='modalContent'></div>";
Modal::end();
$this->registerCssFile('https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css');
$columns = ['Waiting' => 'Waiting', 'Open' => 'Open', 'Progress' => 'In Progress', 'Done' => 'Done', 'Merge' => 'Merge'];
?>
<div class="filter-bar flex flex-row justify-between">
  <div class="w-full flex flex-row justify-end gap-2">
    <div class="search-add-bar">
      <form method="get" action="<?= Url::to(['index']) ?>">
        <?= Html::hiddenInput('viewMode', 'table') ?>
        <?= Html::dropDownList('status', $statusView, $columns, ['class' => 'form-select form-select-sm', 'onchange' => 'this.form.submit()']) ?>
      </form>
    </div>
    <div class="search-add-bar">
      <form method="get" action="<?= Url::to(['index']) ?>">
        <?= Html::hiddenInput('status', 'Open') ?>
        <?= Html::dropDownList('viewMode', $viewMode, [
          'task' => 'Task View',
          'board' => 'Board Progress',
          'table' => 'Table View',
        ], ['class' => 'form-select form-select-sm', 'onchange' => 'this.form.submit()']) ?>
      </form>
    </div>
    <?= Html::button(
      '<i class="fas fa-plus"></i> Task',
      [
        'value' => Url::to(['create']),
        'title' => 'Form Task',
        'class' => 'showModalButton btn btn-primary',
        'disabled' => $jumlahTask == true ? true : false,
      ]
    ) ?>

  </div>
</div>

<div class="table-view">
  <h3>Task Table</h3>

  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'bordered' => false,
    'striped' => false,
    'summary' => '', // Menghilangkan "Showing x of y" summary
    'tableOptions' => [
      'class' => 'table w-full', // kamu bisa tambahkan tailwind class di sini kalau pakai
      'style' => 'border-collapse: collapse; background-color: white;',
    ],
    'rowOptions' => function () {
      return ['style' => 'border: none; color: black;'];
    },
    'columns' => [
      [
        'class' => 'yii\grid\SerialColumn',
        'header' => 'No',
        'headerOptions' => [
          'style' => 'background-color: #E5E7EB; border: none; color: #3F4254; text-align: center;'
        ],
        'contentOptions' => [
          'style' => 'border: none; text-align: center; background-color: white;'
        ],
      ],
      [
        'attribute' => 'title',
        'headerOptions' => ['style' => 'background-color: #E5E7EB; border: none; color: #3F4254'],
        'contentOptions' => ['style' => 'border: none;'],
      ],
      [
        'attribute' => 'assign',
        'headerOptions' => ['style' => 'background-color: #E5E7EB; border: none; color: #3F4254'],
        'contentOptions' => ['style' => 'border: none;'],
      ],
      [
        'attribute' => 'duedate_task',
        'format' => ['date', 'php:d-m-Y'],
        'headerOptions' => ['style' => 'background-color: #E5E7EB; border: none; color: #3F4254'],
        'contentOptions' => ['style' => 'border: none;'],
      ],
      [
        'attribute' => 'description',
        'format' => 'ntext',
        'headerOptions' => ['style' => 'background-color: #E5E7EB; border: none; color: #3F4254'],
        'contentOptions' => ['style' => 'border: none;'],
      ],
      [
        'attribute' => 'priority_task',
        'headerOptions' => ['style' => 'background-color: #E5E7EB; border: none; color: #3F4254'],
        'contentOptions' => ['style' => 'border: none;'],
      ],
      [
        'attribute' => 'status',
        'headerOptions' => ['style' => 'background-color: #E5E7EB; border: none; color: #3F4254'],
        'contentOptions' => ['style' => 'border: none;'],
      ],
      [
        'class' => 'yii\grid\ActionColumn',
        'template' => '{update} {delete}',
        'headerOptions' => ['style' => 'background-color: #E5E7EB; border: none; text-align: center; color: #3F4254'],
        'contentOptions' => ['style' => 'border: none; text-align: center;'],
        'buttons' => [
          'update' => function ($url, $model) {
            return Html::a('<i class="fas fa-edit"></i>', '#', [
              'class' => 'showModalButton text-blue-600',
              'title' => 'Edit Task',
              'value' => Url::to(['/task/task/view', 'id_task' => $model->id_task]),
            ]);
          },
          'delete' => function ($url, $model) {
            return Html::button('<i class="fas fa-trash"></i>', [
              'class' => 'delete-task text-red-600 border-0 bg-transparent',
              'data-id' => $model->id_task,
              'title' => 'Hapus',
            ]);
          }
        ]
      ],
    ],
  ]) ?>

  <?php
  JSRegister::begin(['position' => View::POS_END]);
  ?>
  <script>
    $(document).ready(function() {

      $(document).on('click', '.showModalButton', function() {
        $('#modal').modal('show')
          .find('#modalContent')
          .load($(this).attr('value'), function() {
            // Inisialisasi CKEditor di sini setelah modal selesai load
            setTimeout(function() {
              $('.task-form textarea').each(function() {
                var editorId = $(this).attr('id');
                if (editorId) {
                  if (CKEDITOR.instances[editorId]) {
                    CKEDITOR.instances[editorId].destroy(true);
                  }
                  CKEDITOR.replace(editorId);
                }
              });
            }, 300); // kasih delay agar DOM benar-benar siap
          });
      });

      $(document).on('click', '.delete-task', function() {
        const id = $(this).data('id');

        Swal.fire({
          title: 'Yakin ingin menghapus?',
          text: "Data tidak bisa dikembalikan!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#3085d6',
          confirmButtonText: 'Ya, hapus!'
        }).then((result) => {
          if (result.isConfirmed) {
            fetch('/task/task/delete?id=' + id, {
                method: 'POST',
                headers: {
                  'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                }
              })
              .then(response => {
                if (response.redirected) {
                  window.location.href = response.url;
                } else {

                }
              });
          }
        });
      });

    });
  </script>
  <?php JSRegister::end(); ?>
</div>