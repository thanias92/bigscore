<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\grid\ActionColumn;
use yii\web\View;
use app\widgets\JSRegister;
use kartik\grid\GridView;
use kartik\daterange\DateRangePicker;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\ticketing\FeedbackSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'FEEDBACK';
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
  body {
    background-color: rgba(245, 248, 250, 1) !important;
    overflow: hidden;
  }

  .feedback-table thead th {
    background-color: #E5E7EB !important;
    /* Warna latar header */
    color: #3F4254 !important;
    /* Warna tulisan header */
    padding: 10px;
    text-align: left;
    font-family: 'Inter Semi Bold', sans-serif;
    /* Font header */
    font-weight: 600;
    border-bottom: 1px solidrgb(174, 170, 174);
    /* stroke bawah */
    position: sticky;
    top: 0;
    z-index: 1;
    margin-bottom: 20px;
  }

  .feedback-table tbody td {
    background-color: white !important;
    color: #191919 !important;
    padding: 10px;
    border-bottom: 1px solid rgba(192, 191, 192, 1);
    font-family: 'Inter Regular', sans-serif;
    /* Font Inter Regular */
    cursor: pointer;
    /* Tambahkan cursor pointer untuk indikasi bisa diklik */
  }


  /* === CUSTOMER CONTAINER === */
  .feedback-container {
    background-color: #ffffff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
    display: flex;
    flex-direction: column;
    height: calc(100vh - 100px);
    /* Sesuaikan tinggi container */
    overflow-y: auto;
  }

  /* === HEADER === */
  .feedback-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
  }

  .feedback-title {
    color: rgba(94, 98, 120, 1);
    font-family: 'Inter Semi Bold', sans-serif;
    /* Font Inter Semi Bold */
    font-weight: 600;
    font-size: 1.5rem;
  }

  /* === SEARCH + ADD BUTTON === */
  .search-add-bar {
    display: flex;
    gap: 10px;
  }

  .btn-feedback {
    background-color: #e5e7eb;
    /* abu-abu */
    color: #1f2937;
    /* teks abu gelap */
    border: none;
    border-radius: 50px;
    font-weight: 500;
  }

  .btn-feedback:hover {
    background-color: #2563eb;
    /* biru saat hover */
    color: white;
  }

  .search-input-group {
    background-color: rgba(229, 231, 235, 1);
    border: 1px solid rgba(192, 191, 192, 1);
    border-radius: 5px;
    overflow: hidden;
    display: flex;
    align-items: center;
    height: 30px;
    /* Perkecil tinggi */
  }

  .search-input {
    border: none;
    background-color: transparent;
    padding: 15px 1px 2px 8px;
    flex-grow: 1;
    font-family: 'Inter Regular', sans-serif;
    /* Font Inter Regular */
    font-size: 0.9rem;
    /* Perkecil ukuran font */
  }

  .search-input::placeholder {
    color: rgba(113, 128, 150, 0.8);
  }

  .search-button {
    background-color: transparent;
    border: none;
    padding: 4px 8px;
    /* Perkecil padding vertikal */
    color: rgb(122, 136, 158);
    font-size: 0.9rem;
    /* Perkecil ukuran font */
    border-left: 1px solid rgba(192, 191, 192, 1);
    /* Tambah garis kiri */
  }

  .search-button i {
    color: rgba(72, 129, 173, 1);
    /* Ganti warna ikon search */
  }


  .filter-group input {
    background-color: rgba(229, 231, 235, 1);
    border: 1px solid rgba(192, 191, 192, 1);
    border-radius: 5px;
    overflow: hidden;
    display: flex;
    align-items: center;
    height: 30px;
    padding: 4px 8px;
    border-radius: 8px;
    border: 1px solid #ccc;
    min-width: 150px;
    font-size: 0.9rem;
    border-left: 1px solid rgba(192, 191, 192, 1);
  }

  .filter-group {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-left: auto;
    /* ✅ Tambahkan ini */
    margin-top: 15px;
    /* ✅ Tambahkan ini */
  }

  .date-separator {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 30px;
  padding: 0 4px;
  font-weight: bold;
  color: #333;
   position: relative;
  top: -6px; /* naikkan sedikit */
}
</style>

<?php yii\bootstrap5\Modal::begin([
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
yii\bootstrap5\Modal::end();
?>


<?php Pjax::begin(['id' => 'gridDatafeedback', 'timeout' => false, 'enablePushState' => true, 'enableReplaceState' => false]); ?>
<div class="feedback-container">
  <div class="feedback-header mb-2 d-flex justify-between align-items-center">
    <h2 class="feedback-title"><?= Html::encode($this->title) ?></h2>
  </div>

  <div class="d-flex justify-between align-items-center gap-2 ms-auto">

    <!-- SEARCH -->
    <div class="search-input-group d-flex align-items-center gap-1">
      <?php $form = \yii\widgets\ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => ['class' => 'd-flex align-items-center mb-0'],
      ]); ?>

      <?= $form->field($searchModel, 'queryString')->textInput([
        'class' => 'search-input',
        'placeholder' => 'Search ...',
      ])->label(false) ?>

      <button class="search-button" type="submit">
        <i class="fas fa-search"></i>
      </button>

      <?php \yii\widgets\ActiveForm::end(); ?>
    </div>

    <!-- FILTER TANGGAL -->
    <div class="filter-group d-flex align-items-center gap-2">
      <?php $form = \yii\widgets\ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => ['class' => 'd-flex align-items-center mb-0'],
      ]); ?>

      <?= $form->field($searchModel, 'start_date')->widget(DatePicker::classname(), [
        'options' => ['placeholder' => 'Start Date', 'style' => 'margin-right:10px;'],
        'type' => DatePicker::TYPE_INPUT,
        'pluginOptions' => [
          'autoclose' => true,
          'format' => 'yyyy-mm-dd'
        ]
      ])->label(false) ?>

      <!-- TANDA PENGHUBUNG -->
  <span class="date-separator">-</span>


      <?= $form->field($searchModel, 'end_date')->widget(DatePicker::classname(), [
        'options' => ['placeholder' => 'End Date'],
        'type' => DatePicker::TYPE_INPUT,
        'pluginOptions' => [
          'autoclose' => true,
          'format' => 'yyyy-mm-dd'
        ]
      ])->label(false) ?>

      <button class="btn p-1" type="submit" title="Filter">
        <i class="fas fa-filter text-primary"></i>
      </button>

      <a href="<?= Url::to(['index']) ?>" class="btn p-1" title="Reset">
        <i class="fas fa-sync text-primary"></i>
      </a>

      <?php \yii\widgets\ActiveForm::end(); ?>
    </div>

  </div>

  <div class="card-body">
    <div class="feedback-index col-lg-12 p-0 table-responsive">

      <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'pjax' => true,
        'tableOptions' => ['class' => 'feedback-table table-sm'],
        'bordered' => false,
        'hover' => true,
        //'filterModel' => $searchModel,
        'columns' => [
          ['class' => 'yii\grid\SerialColumn'],
          [
            'header' => 'customer',
            'attribute' => 'id_deals',
            'label' => 'Customer',
            'format' => 'raw',
            'value' => function ($model) {
              return $model->deals
                ? Html::encode($model->deals->customer->customer_name)
                : '-';
            }
          ],
          [
            'header' => 'date feedback',
            'attribute' => 'date_feedback'
          ],
          [
            'header' => 'Product',
            'attribute' => 'id_deals',
            'label' => 'Product',
            'format' => 'raw',
            'value' => function ($model) {
              return $model->deals && $model->deals->product
                ? Html::encode($model->deals->product->product_name) // sesuaikan dengan nama kolom
                : '-';
            }
          ],
          [
            'header' => 'Rate',
            'attribute' => 'rate'
          ],
          //'created_by',
          //'updated_by',
          //'deleted_by',
          //'created_at',
          //'updated_at',
          //'deleted_at',
          [
            'header' => 'Feedback',
            'attribute' => 'feedback',
            'format' => 'raw',
            'hAlign' => 'right',
            'contentOptions' => ['class' => 'text-center align-middle'],
            'headerOptions' => ['class' => 'text-center'],
            'value' => function ($model) {
              return
                '<button type="button" class="btn btn-sm rounded-pill btn-feedback show_ulasan" data-rate="' . $model->rate . '" data-ulasan="' . $model->feedback . '" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
  <i class="fas fa-eye me-1"></i> Feedback
</button>' .
                Html::a('') .
                Html::a('');
            }
          ],
        ],
      ]); ?>


    </div>
  </div>
</div>

<?php Pjax::end(); ?>

<!-- Modal -->
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <div class="w-100 text-center">
          <h1 class="modal-title fs-5" id="staticBackdropLabel">Feedback</h1>
        </div>

        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="w-100 text-center">
          <i id="icon-rate"></i>
          <h1 class="modal-title fs-5" id="rate_value"></h1>
          <p id="rate_ulasan">
          </p>
        </div>

      </div>
      <div class="modal-footer">
        <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Understood</button> -->
      </div>
    </div>
  </div>
</div>

<?php
$link_delete = Url::to(['delete']);

JSRegister::begin(['position' => View::POS_END]);
?>
<script>
  let timer = null;
  let lastQuery = '';

  // Input listener untuk search feedback
  $(document).on('input', '#search-input', function() {
    lastQuery = $(this).val();
    clearTimeout(feedback_timer);

    if (lastQuery.length >= 3 || lastQuery.length === 0) {
      timer = setTimeout(() => {
        $.pjax.reload({
          container: '#gridDatafeedback',
          url: '<?= Url::to(['index']) ?>' + '?FeedbackSearch[queryString]=' + encodeURIComponent(lastQuery),
          replace: false,
          push: false,
          timeout: 1000,
        });
      }, 400); // Delay 400ms
    }
  });

  // Setelah reload PJAX, balikin nilai ke input biar gak kosong
  $(document).on('pjax:end', function() {
    $('#search-input').val(lastQuery);
  });


  $(".show_ulasan").on("click", function() {
    let rate_ulasan = $(this).data("ulasan");
    let rate_value = $(this).data("rate");
    let icon = '<i class="fas fa-smile fa-3x"></i>';
    if (rate_value == 'Tidak Puas') {
      rate_value = 'Bad';
      icon = '<i class="fas fa-frown fa-3x"></i>'
    } else if (rate_value == 'Puas') {
      rate_value = 'Good';
      icon = '<i class="fas fa-smile fa-3x"></i>'
    } else if (rate_value == 'Sangat Puas') {
      rate_value = 'Verry Good';
      icon = '<i class="fas fa-laugh fa-3x"></i>'
    }

    $("#rate_ulasan").text(rate_ulasan);
    $("#rate_value").text(rate_value);
    $("#icon-rate").html(icon);
  })
  var is_fetch = false;
  $(document).on("click", ".delete", function() {
    var id = $(this).attr("id_feedback");
    var link_delete = "<?= $link_delete; ?>?id_feedback=" + id;
    Swal.fire({
      title: 'Konfirmasi',
      text: 'Apakah Akan Menghapus Data?',
      showCancelButton: true,
      confirmButtonText: 'Lanjutkan',
      cancelButtonText: 'Batal',
      icon: 'question',
      customClass: {
        confirmButton: 'my-confirm-button-class'
      },
    }).then((result) => {
      if (result.isConfirmed) {
        if (is_fetch == false) {
          is_fetch = true;
          $.ajax({
            type: "POST",
            url: link_delete,
            dataType: "html",
            success: function(response) {
              const backRes = JSON.parse(response);
              if (backRes.status == 'success') {
                Swal.fire('Ok', backRes.message, 'success');
                $.pjax.reload({
                  container: "#gridDatafeedback"
                });
              } else {
                Swal.fire('Terjadi Kesalahan.', backRes.message, 'warning');
              }
              is_fetch = false
            }
          });
        }
      }
    })
  });
</script>
<?php JSRegister::end(); ?>