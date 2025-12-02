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

$columns = ['Open' => 'Open', 'In Progress' => 'In Progress', 'Done' => 'Done', 'Merge' => 'Merge'];
$taskOptions = ['Task', 'Board Progress', 'Table'];
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
    position: relative;
    z-index: 1;
    /* Ensure the card is above any other elements */
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
    /* Font kecil */
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

  /* Background container to avoid issues with card layout */
  .task-list-container {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    /* Adjust the gap between cards */
    margin: 0;
    /* Remove unwanted margin */
    padding: 0;
  }

  /* Kelas untuk tombol 'Tambah Fitur' */
  .custom-btn-tambah {
    background-color: rgba(39, 70, 94, 1) !important;
    color: white !important;
    border: none !important;
    padding: 10px 20px;
    border-radius: 5px;
    transition: background-color 0.3s ease;
  }

  .custom-btn-tambah:hover {
    background-color: rgba(39, 70, 94, 0.8) !important;
    /* Efek hover */
  }


  /* Ensure background is clear */
  .task-list-container .task-item {
    background: transparent;
    /* Make sure background is transparent */
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

<?php Pjax::begin(['id' => 'gridDatatask', 'timeout' => false, 'enablePushState' => true, 'enableReplaceState' => false]); ?>

<div class="container">
  <div class="card-header d-flex">
    <div class="header-title flex-grow-1">
      <div class="d-flex align-items-center" style="gap: 15px; margin-top: 10px; justify-content: flex-end;">
        <div style="width:20%;">
          <?php echo $this->render('_search', ['model' => $searchModel]); ?>
        </div>
        <?= Html::button('
    <svg class="icon-16" width="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd" clip-rule="evenodd" d="M14.7366 2.76175H8.08455C6.00455 2.75375 4.29955 4.41075 4.25055 6.49075V17.3397C4.21555 19.3897 5.84855 21.0807 7.89955 21.1167C7.96055 21.1167 8.02255 21.1167 8.08455 21.1147H16.0726C18.1416 21.0937 19.8056 19.4087 19.8026 17.3397V8.03975L14.7366 2.76175Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
        <path d="M14.4741 2.75V5.659C14.4741 7.079 15.6231 8.23 17.0431 8.234H19.7971" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
        <path d="M14.2936 12.9141H9.39355" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
        <path d="M11.8442 15.3639V10.4639" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
    </svg>|Task', ['value' => Url::to(['create']), 'title' => 'Form Task', 'class' => 'showModalButton btn btn-primary custom-btn-tambah']) ?>
      </div>

      <div class="card-body">
        <div class="filter-bar">
          <div class="nav-links">
            <?php foreach ($columns as $col => $key): ?>
              <a class="btn btn-link" href="<?= Url::to(['/task/task/index', 'status' => $col]) ?>" id="status-tab-<?= strtolower($col) ?>">
                <?= $key ?>
              </a>
            <?php endforeach; ?>
            <!-- Task List -->
            <div id="task-list" class="task-list-container">
              <?php Pjax::begin(['id' => 'task-list-pjax']); ?>
              <?php

              echo ListView::widget([
                'dataProvider' => $dataProvider,
                'itemView' => '_task_item',
                'layout' => "<div class='row'>{items}</div>\n{pager}",
                'itemOptions' => ['class' => 'col-lg-3 col-md-6 mb-4'],
              ]);
              ?>
              <?php Pjax::end(); ?>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

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
      const currentStatus = "<?= Yii::$app->request->get('status', 'Open'); ?>";
      $("#status-tab-" + currentStatus.toLowerCase()).addClass("active");
    });
  </script>
  <?php JSRegister::end(); ?>