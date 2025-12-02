<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\grid\ActionColumn;
use yii\web\View;
use app\widgets\JSRegister;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\ticketing\NotificationContractSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $filter string */

$this->title = 'CUSTOMER';
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
  body {
    background-color: rgba(245, 248, 250, 1) !important;
    overflow: hidden;
  }

  .notif-table thead th {
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

  .notif-table tbody td {
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
  .notif-container {
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
  .notif-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
  }

  .notif-title {
    color: rgba(94, 98, 120, 1);
    font-family: 'Inter Semi Bold', sans-serif;
    margin-bottom: 1rem;
    /* jarak bawah dari tulisan CUSTOMER */
    font-weight: 600;
    font-size: 1.5rem;
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
  .add-notif-button {
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

  .add-notif-button:hover {
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

  .done {
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
    'class' => 'fade' // Tambahkan class fade untuk animasi
  ],
]);
echo "<div id='modalContent'></div>";
yii\bootstrap5\Modal::end();
?>
<?php Pjax::begin(['id' => 'gridDatanotification-payment', 'timeout' => false, 'enablePushState' => true, 'enableReplaceState' => false]); ?>
<div class="notif-container">
  <div class="card-header ">
    <h2 class="notif-title mb-3"><?= Html::encode($this->title) ?></h2>
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3 px-3">

      <div class="d-flex gap-2 ">
        <?= Html::a('Notification Payment', ['index', 'filter' => 'notification_payment'], [
          'class' => 'btn ' . ($filter === 'notification_payment' ? 'btn-blue text-white' : 'btn-secondary text-dark border'),
          'style' => 'min-width:100px;'
        ]) ?>
        <?= Html::a('Notification Contract', ['index', 'filter' => 'notification_contract'], [
          'class' => 'btn ' . ($filter === 'notification_contract' ? 'btn-blue text-white' : 'btn-secondary text-dark border'),
          'style' => 'min-width:100px;'
        ]) ?>
      </div>

      <div class="d-flex align-items-center gap-2 mt-2 mt-md-0">
        <!-- Search Form -->
        <div class="search-input-group d-flex align-items-center">
          <?php
          $form = \yii\widgets\ActiveForm::begin([
            'action' => ['index', 'filter' => $filter],
            'method' => 'get',
            'options' => ['class' => 'd-flex align-items-center mb-0'],
          ]);
          ?>
          <?= $form->field($searchModel, 'queryString')->textInput([
            'class' => 'search-input',
            'placeholder' => 'Search ...',
          ])->label(false) ?>
          <button class="search-button"><i class="fa fa-search"></i></button>
          <?php \yii\widgets\ActiveForm::end(); ?>
        </div>

        <!-- Tombol Ticket -->
        <!-- <?php if (Yii::$app->request->get('category') === 'staff' || Yii::$app->request->get('category') === null): ?>
          <?= Html::button('<i class="fas fa-plus"></i> Ticket', [
                  'value' => Url::to(['create']),
                  'title' => 'Form Ticket',
                  'class' => 'showModalButton add-notif-button'
                ]) ?>
        <?php endif; ?> -->
      </div>
    </div>
  </div>

  <div class="card-body">

    <div class="notification-payment-index col-lg-12 p-0 table-responsive">
      <?php
      $columns = [];

      if ($filter === 'notification_payment') {
        $columns = [
          ['class' => 'yii\grid\SerialColumn'],
          [
            'header' => 'Customer',
            'attribute' => 'id_deals',
            'format' => 'raw',
            'value' => function ($model) {
              $customer = $model->pemasukan->deals->customer ?? null;
              return $customer && !empty($customer->customer_name)
                ? Html::a(Html::encode($customer->customer_name), '#', [
                  'value' => Url::to(['view', 'type' => 'payment', 'id' => $model->id_notification_payment]),
                  'title' => 'Detail Notifikasi Payment',
                  'class' => 'showModalButton text-primary'
                ])
                : '-';
            },
          ],
          [
            'label' => 'Product',
            'value' => function ($model) {
              return $model->pemasukan->deals->product->product_name ?? '-';
            }
          ],
          [
            'label' => 'Email',
            'value' => function ($model) {
              return $model->pemasukan->deals->customer->customer_email ?? '-';
            }
          ],
          [
            'label' => 'Telepon',
            'value' => function ($model) {
              return $model->pemasukan->deals->customer->customer_phone ?? '-';
            }
          ],
          [
            'label' => 'Status',
            'value' => function ($model) {
              return $model->pemasukan->status ?? '-';
            },
            'format' => 'raw',
          ],
        ];
      } elseif ($filter === 'notification_contract') {
        $columns = [
          ['class' => 'yii\grid\SerialColumn'],
          [
            'label' => 'Customer',
            'format' => 'raw',
            'value' => function ($model) {
              $customer = $model->contract->invoice->deals->customer ?? null;
              return $customer
                ? Html::a(Html::encode($customer->customer_name), '#', [
                  'value' => Url::to(['view', 'type' => 'contract', 'id' => $model->id_notification_contract]),
                  'title' => 'Detail Notifikasi Kontrak',
                  'class' => 'showModalButton text-primary'
                ])
                : '-';
            },
          ],
          [
            'label' => 'Product',
            'value' => fn($model) => $model->contract->invoice->deals->product->product_name ?? '-'
          ],
          [
            'label' => 'Start Date',
            'value' => function ($model) {
              return $model->contract->start_date ?? '-';
            },
          ],

          [
            'label' => 'End Date',
            'value' => fn($model) => $model->contract->end_date ?? '-'
          ],
          [
            'label' => 'Status',
            'value' => fn($model) => $model->contract->status_contract ?? '-'
          ],
        ];
      }
      ?>

      <!-- Render jadi satu -->
      <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'pjax' => true,
        'tableOptions' => ['class' => 'notif-table table-sm'],
        'bordered' => false,
        'hover' => true,
        'columns' => $columns,
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
  // Trigger Modal ketika tombol dengan class .showModalButton diklik
  $(document).on('click', '.showModalButton', function() {
    $('#modal').modal('show')
      .find('#modalContent')
      .load($(this).attr('value'));
  });

  // Contoh AJAX untuk debug (jika masih dibutuhkan)
  $.get('/ticketing/notification-payment/get-deals?deals_id=3', function(res) {
    if (res.status === 'success') {
      console.log(res.data.customer_name, res.data.product_name);
    }
  });

  // Hapus data notifikasi
  var is_fetch = false;
  $(document).on("click", ".delete", function() {
    var id = $(this).attr("id_notification");
    var link_delete = "<?= $link_delete; ?>?id_notification=" + id;

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
      if (result.isConfirmed && !is_fetch) {
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
                container: "#gridDatanotification-payment"
              });
            } else {
              Swal.fire('Terjadi Kesalahan.', backRes.message, 'warning');
            }
            is_fetch = false;
          }
        });
      }
    });
  });
</script>
<?php JSRegister::end(); ?>