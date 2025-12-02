<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\grid\ActionColumn;
use yii\web\View;
use app\widgets\JSRegister;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\ticketing\TicketingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'TICKETING';
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
  body {
    background-color: rgba(245, 248, 250, 1) !important;
    overflow: hidden;
  }

  /* === SEARCH + ADD BUTTON === */
  .search-add-bar {
    display: flex;
    gap: 10px;
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
    font-family: 'Inter Regular', sans-serif; /* Font Inter Regular */
    font-size: 0.9rem; /* Perkecil ukuran font */
  }

  .search-input::placeholder {
    color: rgba(113, 128, 150, 0.8);
  }

  .search-button {
    background-color: transparent;
    border: none;
    padding: 4px 8px; /* Perkecil padding vertikal */
    color: rgb(122, 136, 158);
    font-size: 0.9rem; /* Perkecil ukuran font */
    border-left: 1px solid rgba(192, 191, 192, 1); /* Tambah garis kiri */
  }

  .search-button i {
    color: rgba(72, 129, 173, 1);
    /* Ganti warna ikon search */
  }

  .add-button {
    background-color: rgba(39, 70, 94, 1);
    color: white;
    border: none;
    padding: 5px 10px;
    /* Perkecil padding vertikal dan horizontal */
    border-radius: 5px;
    font-family: 'Inter Regular', sans-serif;
    /* Font Inter Regular */
    font-size: 0.9rem;
    /* Perkecil ukuran font */
    height: 30px;
    /* Perkecil tinggi */
  }

  .add-button:hover {
    background-color: rgba(39, 70, 94, 0.8);
  }

  .btn.btn-primary {
    background-color: #27465E;
    border-color: #27465E;
  }

  .btn.btn-primary:hover {
    background-color: #1f384d;
    /* warna sedikit lebih gelap untuk hover */
    border-color: #1f384d;
  }

  /* Add Ticket Button */
  .add-ticket-button {
    background-color: rgba(39, 70, 94, 1);
    /* Tetap pakai warna yang kamu minta */
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 5px;
    font-family: 'Inter Regular', sans-serif;
    font-size: 0.9rem;
    height: 30px;
  }

  .add-ticket-button:hover {
    background-color: rgba(39, 70, 94, 0.8);
    /* Efek hover */
  }

  /* Tab Navigation */
  .tab-navigation {
    display: flex;
    gap: 8px;
    margin-bottom: 16px;
    flex-wrap: wrap;
  }

  .tab-button {
    background-color: #f3f4f6;
    border: 1px solid #d1d5db;
    color: #374151;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 14px;
    cursor: pointer;
    transition: background-color 0.2s ease;
  }

  .btn-light-custom {
    background-color: #E5E7EB;
    border: 1px solid #C0BFC0;
    color: #191919;
    /* Warna teks tombol */
  }

  .btn-light-custom:hover {
    background-color: #D1D4D9;
    /* Efek hover, sedikit lebih gelap dari background */
    border-color: #A3A9B3;
    /* Warna border saat hover */
  }

  .btn-secondary {
    background-color: transparent !important;
    border: 1px solid #E1E3EA !important;
    color: #5E6278 !important;
  }

  .btn-secondary:hover {
    background-color: rgba(225, 227, 238, 0.1);
    /* efek hover ringan */
    border-color: #E1E3EA;
    color: #5E6278;
  }

  .btn.btn-blue {
    background-color: #4881AD;
  }

  .btn.btn-blue:hover {
    background-color: #4881AD;
  }

  .label {
    color: #4881AD;
    background-color: rgba(108, 179, 234, 0.15);
    /* 15% opacity dari #6CB3EA */
    /* biru */
    padding: 2px 6px;
    border-radius: 4px;
    font-weight: 600;

  }

  /* Priority Ticket */
  .priority-high {
    color: #AB3129;
    background-color: rgba(253, 236, 238, 0.15);
    /* merah gelap */
    padding: 2px 6px;
    border-radius: 4px;
    font-weight: 600;
  }

  .priority-medium {
    color: #FDAB3D;
    background-color: rgba(253, 171, 61, 0.15);
    /* 15% opacity dari #FDAB3D */
    /* oranye */
    padding: 2px 6px;
    border-radius: 4px;
    font-weight: 600;
  }

  .priority-low {
    color: #1AA119;
    background-color: rgba(26, 161, 25, 0.15);
    /* hijau */
    padding: 2px 6px;
    border-radius: 4px;
    font-weight: 600;
  }

  /* Status Ticket */
  .status-open {
    color: #FDAB3D;
    background-color: rgba(253, 171, 61, 0.15);
    /* oren */
    padding: 2px 6px;
    border-radius: 4px;
    font-weight: 600;
  }

  .status-inprogress {
    color: #4881AD;
    background-color: rgba(72, 129, 173, 0.15);
    /* biru */
    padding: 2px 6px;
    border-radius: 4px;
    font-weight: 600;
  }

  .status-done {
    color: #1AA119;
    background-color: rgba(26, 161, 25, 0.15);
    /* hijau */
    padding: 2px 6px;
    border-radius: 4px;
    font-weight: 600;
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
  'closeButton' => [
    'aria-label' => 'Close',
    'class' => 'btn-close',
    'data-bs-dismiss' => 'modal',
  ],

]);
echo "<div id='modalContent'></div>";
yii\bootstrap5\Modal::end();
?>
<?php Pjax::begin(['id' => 'gridDataticket', 'timeout' => false, 'enablePushState' => true, 'enableReplaceState' => false]); ?>
<div class="card">
  <div class="card-header">
    <div class="title-bar">
      <h4 style="margin-bottom: 16px;">TICKETING</h4>
      <div class="d-flex justify-content-between align-items-center flex-wrap mb-3 px-3">
        <!-- BAGIAN KIRI: Tombol filter -->
        <div class="d-flex gap-2">
          <?= Html::a('Ticket by Staff', ['index', 'category' => 'staff'], [
            'class' => 'btn me-2 ' .
              ((Yii::$app->request->get('category') == 'staff' || Yii::$app->request->get('category') === null)
                ? 'btn-primary text-white'
                : 'btn-secondary text-dark border')
          ]) ?>

          <?= Html::a('Ticket by Customer', ['index', 'category' => 'customer'], [
            'class' => 'btn ' .
              (Yii::$app->request->get('category') == 'customer'
                ? 'btn-blue text-white'
                : 'btn-secondary text-dark border')
          ]) ?>
        </div>
        <div class="d-flex align-items-center gap-2 mt-2 mt-md-0">
          <!-- Search Form -->
          <div class="search-input-group d-flex align-items-center">
            <?php
            $form = \yii\widgets\ActiveForm::begin([
              'action' => ['index'],
              'method' => 'get',
              'options' => ['class' => 'd-flex align-items-center mb-0'],
            ]);
            ?>
            <?= $form->field($searchModel, 'queryString')->textInput([
              'class' => 'search-input',
              'placeholder' => 'Search ...',
            ])->label(false) ?>
            <button class="search-button"><i class="bi bi-search"></i></button>
            <?php \yii\widgets\ActiveForm::end(); ?>
          </div>

          <!-- Tombol Ticket -->
          <?= Html::button('<i class="bi bi-plus"></i> Ticket', [
            'value' => Url::to(['create']),
            'title' => 'Form Ticket',
            'class' => 'showModalButton add-button'
          ]) ?>
        </div>
      </div>
    </div>
    <div class="card-body">
      <div class="ticket-index col-lg-12 p-0 table-responsive">

        <?= GridView::widget([
          'dataProvider' => $dataProvider,
          'pjax' => true,
          'tableOptions' => ['class' => 'ticket-table table-sm'],
          'bordered' => false,
          'hover' => true,
          //'filterModel' => $searchModel,
          'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'code_ticket',
            'role',
            'date_ticket',
            'via',
            'modul',
            [
              'attribute' => 'label_ticket',
              'format' => 'raw',
              'value' => function ($model) {
                return '<span class="label">' . $model->label_ticket . '</span>';
              }
            ],
            [
              'attribute' => 'priority_ticket',
              'format' => 'raw',
              'value' => function ($model) {
                $priority = strtolower($model->priority_ticket);
                return '<span class="priority-' . $priority . '">' . $model->priority_ticket . '</span>';
              }
            ],
            [
              'attribute' => 'status_ticket',
              'format' => 'raw',
              'value' => function ($model) {
                $status = strtolower($model->status_ticket);
                // ubah spasi jadi dash (misal "In Progress" jadi "in-progress")
                $class = 'status-' . str_replace(' ', '', $status);
                return '<span class="' . $class . '">' . $model->status_ticket . '</span>';
              }
            ],
            [
              'header' => 'Actions',
              'format' => 'raw',
              'hAlign' => 'right',
              'value' => function ($model) {
                return Html::a('
        <svg class="icon-16" width="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <circle cx="11.7669" cy="11.7666" r="8.98856" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></circle>
          <path d="M18.0186 18.4851L21.5426 22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
        </svg>
        ', '#', ['value' => Url::to(['view', 'id_ticket' => $model->id_ticket]), 'title' => 'Detail ' . 'Ticket', 'class' => 'showModalButton text-primary me-md-1']) .
                  Html::a('
        <svg class="icon-16" width="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M13.7476 20.4428H21.0002" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
          <path fill-rule="evenodd" clip-rule="evenodd" d="M12.78 3.79479C13.5557 2.86779 14.95 2.73186 15.8962 3.49173C15.9485 3.53296 17.6295 4.83879 17.6295 4.83879C18.669 5.46719 18.992 6.80311 18.3494 7.82259C18.3153 7.87718 8.81195 19.7645 8.81195 19.7645C8.49578 20.1589 8.01583 20.3918 7.50291 20.3973L3.86353 20.443L3.04353 16.9723C2.92866 16.4843 3.04353 15.9718 3.3597 15.5773L12.78 3.79479Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
          <path d="M11.021 6.00098L16.4732 10.1881" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
        </svg>
        ', '#', ['value' => Url::to(['update', 'id_ticket' => $model->id_ticket]), 'title' => 'Form Edit ' . 'Ticket', 'class' => 'showModalButton text-success me-md-1']) .
                  Html::a('
        <svg class="icon-16" width="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M19.3248 9.46826C19.3248 9.46826 18.7818 16.2033 18.4668 19.0403C18.3168 20.3953 17.4798 21.1893 16.1088 21.2143C13.4998 21.2613 10.8878 21.2643 8.27979 21.2093C6.96079 21.1823 6.13779 20.3783 5.99079 19.0473C5.67379 16.1853 5.13379 9.46826 5.13379 9.46826" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
          <path d="M20.708 6.23975H3.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
          <path d="M17.4406 6.23973C16.6556 6.23973 15.9796 5.68473 15.8256 4.91573L15.5826 3.69973C15.4326 3.13873 14.9246 2.75073 14.3456 2.75073H10.1126C9.53358 2.75073 9.02558 3.13873 8.87558 3.69973L8.63258 4.91573C8.47858 5.68473 7.80258 6.23973 7.01758 6.23973" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
        </svg>
        ', '#', ['id_ticket' => $model->id_ticket, 'title' => 'Hapus Data?', 'class' => 'text-danger delete']);
              }
            ],
          ],
        ]); ?>


      </div>
    </div>
  </div>

  <?php Pjax::end(); ?>

  <?php
  $link_delete = Url::to(['delete']);

  JSRegister::begin(['position' => View::POS_END]);
  ?>

  <script>
    var is_fetch = false;

    $(document).on("click", ".delete", function() {
      const id = $(this).attr("id_ticket");
      const link_delete = "<?= $link_delete; ?>?id_ticket=" + id;

      Swal.fire({
        title: 'Konfirmasi',
        text: 'Apakah Akan Menghapus Data?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Lanjutkan',
        cancelButtonText: 'Batal',
        cancelButtonColor: '#d33',
        customClass: {
          confirmButton: 'my-confirm-button-class'
        },
      }).then((result) => {
        if (result.isConfirmed && !is_fetch) {
          is_fetch = true;

          $.ajax({
            type: "POST",
            url: link_delete,
            dataType: "json",
            success: function(res) {
              if (res.status === 'success') {
                Swal.fire('Berhasil', res.message, 'success');
                $.pjax.reload({
                  container: "#gridDataticket"
                });
              } else {
                Swal.fire('Gagal', res.message, 'warning');
              }
            },
            error: function() {
              Swal.fire('Error', 'Gagal menghubungi server.', 'error');
            },
            complete: function() {
              is_fetch = false;
            }
          });
        }
      });
    });

    $('#btn-staff').on('click', function() {
      $.get('<?= Url::to(['load-by-role', 'role' => 'staff']) ?>', function(data) {
        $('#gridDataticket').html(data);
      });
    });

    $('#btn-customer').on('click', function() {
      $.get('<?= Url::to(['load-by-role', 'role' => 'customer']) ?>', function(data) {
        $('#gridDataticket').html(data);
      });
    });


    // function setModalStyle() {
    //   $('#modal').find('.modal-dialog').css({
    //     position: 'fixed',
    //     top: 0,
    //     right: 0,
    //     bottom: 0,
    //     height: '100vh',
    //     width: '400px',
    //     margin: 0,
    //     transform: 'translate(0, 0)'
    //   });

    //   $('#modal').find('.modal-content').css({
    //     height: '100%',
    //     overflowY: 'auto',
    //     borderTopLeftRadius: 0,
    //     borderBottomLeftRadius: 0
    //   });

    //   $('.modal-backdrop').css({
    //     left: 'auto',
    //     right: '400px',
    //     width: 'calc(100% - 400px)'
    //   });
    // }

    function resetModalStyle() {
      $('#modal').find('.modal-dialog').removeAttr('style');
      $('#modal').find('.modal-content').removeAttr('style');
      $('.modal-backdrop').removeAttr('style');
    }

    $(document).on('show.bs.modal', '#modal', setModalStyle);
    $(document).on('hide.bs.modal', '#modal', resetModalStyle);
  </script>
  <?php JSRegister::end(); ?>