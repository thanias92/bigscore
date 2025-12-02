<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\web\View;
use kartik\grid\GridView;
use yii\bootstrap5\Modal;
use app\widgets\JSRegister;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\task\TaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Task';
$this->params['breadcrumbs'][] = $this->title;

$columns = ['Waiting' => 'Waiting', 'Open' => 'Open', 'Progress' => 'In Progress', 'Done' => 'Done', 'Merge' => 'Merge'];
$taskOptions = ['Task', 'Board Progress', 'Table'];
$viewMode = Yii::$app->request->get('viewMode', 'task');

?>

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
    background-color: rgba(229, 231, 235, 1);
    /* Ini background dari grup input search */
    border: 1px solid rgba(192, 191, 192, 1);
    border-radius: 5px;
    overflow: hidden;
    display: flex;
    align-items: center;
    height: 30px;
  }

  .search-input {
    border: none;
    background-color: transparent;
    /* Ini background dari input field search itu sendiri */
    padding: 5px 8px;
    flex-grow: 1;
    font-family: 'Inter Regular', sans-serif;
    font-size: 0.9rem;
  }

  .search-button {
    background-color: transparent;
    border: none;
    padding: 5px 8px;
    color: rgba(113, 128, 150, 1);
    /* Warna teks/ikon di tombol search */
    font-size: 0.9rem;
  }

  .search-button i {
    color: rgba(72, 129, 173, 1);
    /* Warna ikon search, ini yang override warna di .search-button */
  }

  .search-button img {
    width: 15px;
    /* Contoh ukuran, sesuaikan sampai terlihat sama */
    height: 15px;
    /* Sesuaikan agar proporsional */
    /* Tambahkan baris ini untuk menggeser ikon ke bawah */
    padding-top: 4px;
    /* Sesuaikan nilai piksel sesuai kebutuhan */
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

$this->registerCssFile('https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css');
?>

<?php Pjax::begin(['id' => 'gridDatatask', 'timeout' => false, 'enablePushState' => true, 'enableReplaceState' => false]); ?>

<div class="card">
  <div class="card-body">
    <div class="filter-bar flex flex-row justify-between">
      <div class="nav-links">
        <?php foreach ($columns as $col => $key): ?>
          <?php $isActive = strtolower(Yii::$app->request->get('status', 'waiting')) === strtolower($col) ? 'active' : ''; ?>
          <a class="btn btn-link <?= $isActive ?>" href="<?= Url::to(['/task/task/index', 'status' => $col]) ?>">
            <?= $key ?>
          </a>
        <?php endforeach; ?>

      </div>

      <div class="flex flex-row gap-2">
        <div class="search-add-bar">
          <div class="search-input-group">
            <?php $form = \yii\widgets\ActiveForm::begin([
              'action' => ['index'],
              'method' => 'get',
              'options' => [
                'class' => 'flex flex-row justify-between',
              ],
            ]); ?>

            <?= \yii\helpers\Html::hiddenInput('status', $statusView) ?>


            <?= \yii\helpers\Html::textInput('queryString', $queryString, [
              'class' => 'search-input',
              'placeholder' => 'Search ...',
            ]) ?>

            <button class="search-button">
              <img src="/img/search.svg" alt="">
            </button>

            <?php \yii\widgets\ActiveForm::end(); ?>
          </div>
          <form method="get" action="<?= Url::to(['index']) ?>">
            <?= Html::dropDownList('viewMode', $viewMode, [
              'task' => 'Task View',
              'board' => 'Board Progress',
              'table' => 'Table View',
            ], ['class' => 'form-select form-select-sm', 'onchange' => 'this.form.submit()']) ?>
          </form>
        </div>
        <?= Html::button('<i class="fas fa-plus"></i> Task', ['value' => Url::to(['create']), 'title' => 'Form ' . 'Task', 'class' => 'showModalButton btn btn-primary']) ?>
      </div>
    </div>

    <div id="task-list" class="task-list-container py-4 border-t">
      <?php Pjax::begin(['id' => 'task-list-pjax']); ?>
      <?php

      echo ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView' => '_task_item',
        'layout' => "<div class='grid grid-cols-4 gap-4'>{items}</div>\n<div class='mt-4'>{pager}</div>",
        'itemOptions' => ['tag' => false],
        'pager' => [
          'options' => ['class' => 'pagination flex flex-wrap gap-2'],
          'linkOptions' => ['class' => 'px-3 py-1 bg-white border rounded hover:bg-gray-100'],
          'activePageCssClass' => 'bg-blue-500 text-white',
          'disabledPageCssClass' => 'text-gray-400',
          'nextPageLabel' => '>',
          'prevPageLabel' => '<',
          'firstPageLabel' => '<<',
          'lastPageLabel' => '>>',
        ],
      ]);

      ?>
      <?php Pjax::end(); ?>

    </div>
  </div>
</div
  <?php Pjax::end(); ?>

  <?php
  JSRegister::begin(['position' => View::POS_END]);
  ?>
  <script>
$(document).on('click', '.showModalButton', function() {
$('#modal').modal('show').find('#modalContent').load($(this).attr('value'));
});

// Menambahkan kelas active pada tab yang diklik
$(document).ready(function() {
$(".nav-links .btn-link").on("click", function() {
$(".nav-links .btn-link").removeClass("active");
$(this).addClass("active");
});

// Menandai tab aktif berdasarkan status
const currentStatus = "<?= Yii::$app->request->get('status', 'Waiting'); ?>";
$("#status-tab-" + currentStatus.toLowerCase()).addClass("active");
});
</script>
<?php JSRegister::end(); ?>

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


  // Menambahkan kelas active pada tab yang diklik
  $(document).ready(function() {
    $(".nav-links .btn-link").on("click", function() {
      $(".nav-links .btn-link").removeClass("active");
      $(this).addClass("active");
    });

    // Menandai tab aktif berdasarkan status



    $(document).on('click', '.delete-task', function() {
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
                // Bisa tambahkan alert atau reload
                location.reload();
              }
            });
        }
      });
    });



  });
</script>
<?php JSRegister::end(); ?>