<?php
// protected/modules/task/views/task/board_progress.php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ListView;
use yii\bootstrap5\Modal;
use dosamigos\ckeditor\CKEditor;
use app\widgets\JSRegister;
use yii\widgets\Pjax;
use yii\web\View;
/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 */

 
?>
<style>
    /* Board Progress Container */
    .board-progress {
        display: flex;
        justify-content: space-between;
        gap: 20px;
        margin-top: 20px;
        padding: 20px;
        background-color: #ffffff;
        border-radius: 10px;
        flex-wrap: wrap;
    }

    /* Kolom Tugas (Open, In Progress, Done, Merge) */
    .task-column {
        width: 23%;
        /* Set kolom menjadi 23% dari lebar kontainer */
        background-color: transparant;
        padding: 10px;
        min-height: 400px;
        display: flex;
        flex-direction: column;
        transition: all 0.3s ease-in-out;
    }

    /* Judul Kolom */
    .task-column h3 {
        font-size: 1.2rem;
        font-weight: bold;
        margin-bottom: 15px;
        text-align: center;
        color: #333;
    }

    /* Tugas Item */
    .task-item {
        background-color: #fff;
        border-radius: 8px;
        padding: 15px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        margin-bottom: 15px;
        display: flex;
        flex-direction: column;
        position: relative;
        transition: transform 0.3s ease;
    }

    /* Hover Effect pada Task Item */
    .task-item:hover {
        transform: translateY(-5px);
    }

    /* Tanggal di Kanan Atas */
    .task-item .task-date {
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 12px;
        color: #666;
        background-color: #fff;
        padding: 4px 10px;
        border-radius: 5px;
    }

    /* Nama Modul */
    .task-item .modul-name {
        font-size: 0.9rem;
        color: #333;
        margin-bottom: 10px;
    }

    /* Assigned Task (Nama yang ditugaskan) */
    .task-item .assigned-task {
        display: flex;
        align-items: center;
        font-size: 0.9rem;
        color: #333;
        margin-bottom: 10px;
    }

    .task-item .assigned-task img {
        border-radius: 50%;
        width: 20px;
        height: 20px;
        margin-right: 8px;
    }

    /* Status Task dan Label */
    .task-item .task-status {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .task-item .priority {
        font-size: 0.8rem;
        padding: 4px 8px;
        border-radius: 5px;
        background-color: #f1f1f1;
        color: #007bff;
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
        font-size: 0.8rem;
        background-color: #e0f7ff;
        color: #007bff;
        padding: 4px 8px;
        border-radius: 5px;
    }

    .task-item .details {
        position: absolute;
        bottom: 10px;
        right: 10px;
        font-size: 0.9rem;
        color: #007bff;
        cursor: pointer;
        font-weight: bold;
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
        padding: 0;
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

    /* Warna untuk status kolom */
    .status-open {
        color: #7a7a7a;
    }

    .status-in-progress {
        color: #f0ad4e;
    }

    .status-done {
        color: #28a745;
    }

    .status-merge {
        color: #007bff;
    }

    /* Bulatan warna di status */
    .status-open-bullet {
        background-color: #7a7a7a;
    }

    .status-in-progress-bullet {
        background-color: #f0ad4e;
    }

    .status-done-bullet {
        background-color: #28a745;
    }

    .status-merge-bullet {
        background-color: #007bff;
    }

    /* Mengubah posisi teks menjadi di kiri */
    .task-column h3 {
        font-size: 1.2rem;
        font-weight: bold;
        margin-bottom: 15px;
        text-align: left;
        /* Ubah text alignment ke kiri */
        color: #333;
        display: flex;
        align-items: center;
        padding-left: 10px;
        /* Memberi sedikit ruang ke kiri */
    }

    .status-bullet {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        margin-right: 10px;
        /* Memberi jarak antara bulatan dan teks */
        display: inline-block;
    }


    /* Responsive untuk layar kecil */
    @media (max-width: 768px) {
        .board-progress {
            flex-direction: column;
        }

        .task-column {
            width: 100%;
            margin-bottom: 20px;
        }
    }

    /* Mengatur tampilan board agar rapi */
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
?>
<div class="filter-bar flex flex-row justify-between">
    <div class="w-full flex flex-row justify-end gap-2">
        <div class="search-add-bar">
            <form method="get" action="<?= Url::to(['index']) ?>">
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
            'title' => 'Form ' . 'Task', 
            'class' => 'showModalButton btn btn-primary',
            'disabled' => $jumlahTask == true ? true : false,
            ]) ?>
    </div>
</div>

<div class="board-progress">
    <div class="task-column">
        <h3><span class="status-bullet status-open-bullet"></span> Open</h3>
        <?= ListView::widget([
            'dataProvider' => $openDataProvider,
            'itemView' => '_task_item',
            'layout' => '{items}<div class="mt-2">{pager}</div>',
            'itemOptions' => ['tag' => false],
            'pager' => [
                'options' => ['class' => 'pagination'],
                'linkOptions' => ['class' => 'page-link'],
                'prevPageLabel' => '<',
                'nextPageLabel' => '>',
            ]
        ]) ?>
    </div>

    <div class="task-column">
        <h3><span class="status-bullet status-in-progress-bullet"></span> In Progress</h3>
        <?= ListView::widget([
            'dataProvider' => $progressDataProvider,
            'itemView' => '_task_item',
            'layout' => '{items}<div class="mt-2">{pager}</div>',
            'itemOptions' => ['tag' => false],
        ]) ?>
    </div>

    <div class="task-column">
        <h3><span class="status-bullet status-done-bullet"></span> Done</h3>
        <?= ListView::widget([
            'dataProvider' => $doneDataProvider,
            'itemView' => '_task_item',
            'layout' => '{items}<div class="mt-2">{pager}</div>',
            'itemOptions' => ['tag' => false],
        ]) ?>
    </div>

    <div class="task-column">
        <h3><span class="status-bullet status-merge-bullet"></span> Merge</h3>
        <?= ListView::widget([
            'dataProvider' => $mergeDataProvider,
            'itemView' => '_task_item',
            'layout' => '{items}<div class="mt-2">{pager}</div>',
            'itemOptions' => ['tag' => false],
        ]) ?>
    </div>

</div>

<?php
JSRegister::begin(['position' => View::POS_END]);
?>
<script>
  $(document).on('click', '.showModalButton', function() {
    $('#modal').modal('show')
      .find('#modalContent')
      .load($(this).attr('value'), function() {
        // Inisialisasi CKEditor di sini setelah modal selesai load
        setTimeout(function() {
          $('.task-form textarea').each(function () {
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


  // Menambahkan kelas active pada tab yang diklik
  $(document).ready(function() {
    $(".nav-links .btn-link").on("click", function() {
      $(".nav-links .btn-link").removeClass("active");
      $(this).addClass("active");
    });

    // Menandai tab aktif berdasarkan status
    const currentStatus = "<?= Yii::$app->request->get('status', 'Open'); ?>";
    $("#status-tab-" + currentStatus.toLowerCase()).addClass("active");


    $(document).on('click', '.delete-task', function () {
      const id = $(this).data('id');
      const returnUrl = encodeURIComponent($(this).data('return'));
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
          fetch('/task/task/delete?id=' + id + '&returnUrl=' + returnUrl, {
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