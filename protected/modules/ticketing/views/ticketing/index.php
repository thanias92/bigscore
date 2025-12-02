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
/* @var $searchModel app\modules\ticketing\TicketingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'TICKETING';
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
  body {
    background-color: rgba(245, 248, 250, 1) !important;
  }

  .ticket-table thead th {
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

  .ticket-table tbody td {
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
  .ticket-container {
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
  .ticket-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
  }

  .ticket-title {
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
  .status-waiting {
    color: #151615ff;
    background-color: rgba(37, 40, 37, 0.15);
    /* hijau */
    padding: 2px 6px;
    border-radius: 4px;
    font-weight: 600;
  }

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

  .btn-back {
    background-color: rgba(217, 217, 217, 1);
    color: rgba(0, 0, 0, 1);
    font-family: 'Inter Regular', sans-serif;
    border: 1px solid rgba(217, 217, 217, 1);
    padding: 0.5rem 1rem;
    border-radius: 5px;
    cursor: pointer;
    text-decoration: none;
  }

  .btn-back:hover {
    opacity: 0.8;
  }

  .btn-submit {
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

  .btn-submit:hover {
    background-color: rgba(39, 70, 94, 0.8);
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
    top: -6px;
    /* naikkan sedikit */
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
<?php Pjax::begin(['id' => 'gridDataticket', 'timeout' => false, 'enablePushState' => true, 'enableReplaceState' => false]); ?>
<div class="ticket-container">
  <div class="card-header">
    <h2 class="ticket-title"><?= Html::encode($this->title) ?></h2>
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3 px-3">
      <!-- BAGIAN KIRI: Tombol filter -->
      <div class="d-flex gap-2">
        <?= Html::a('Ticket by Staff', ['index', 'category' => 'staff'], [
          'class' => 'btn me-2 ' .
            ((Yii::$app->request->get('category') == 'staff' || Yii::$app->request->get('category') === null)
              ? 'btn-blue text-white'
              : 'btn-secondary text-dark border')
        ]) ?>

        <?= Html::a('Ticket by Customer', ['index', 'category' => 'customer'], [
          'class' => 'btn ' .
            (Yii::$app->request->get('category') == 'customer'
              ? 'btn-blue text-white'
              : 'btn-secondary text-dark border')
        ]) ?>
      </div>
      <div class="d-flex justify-between align-items-center gap-2 ms-auto">
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


        <!-- Tombol Ticket -->
        <?php if (Yii::$app->request->get('category') === 'staff' || Yii::$app->request->get('category') === null): ?>
          <?= Html::button('<i class="fas fa-plus"></i> Ticket', [
            'value' => Url::to(['create']),
            'title' => 'Form Ticket',
            'class' => 'showModalButton add-ticket-button'
          ]) ?>
        <?php endif; ?>

      </div>
    </div>
  </div>

  <div class="card-body">
    <div class="ticket-index col-lg-12 p-0 table-responsive">
      <?php
      $category = Yii::$app->request->get('category', 'staff'); // default ke staff
      $columns = [
        ['class' => 'yii\grid\SerialColumn'],
        [
          'header' => 'Customer',
          'attribute' => 'id_deals',
          'format' => 'raw',
          'value' => function ($model) {
            $customerName = $model->deals->customer->customer_name ?? '-';

            // Ambil kategori dari URL
            $category = Yii::$app->request->get('category', 'staff');

            if ($category === 'staff') {
              // Jika staff: buka halaman view-only (misalnya 'view')
              return Html::a(
                $customerName,
                '#',
                [
                  'value' => \yii\helpers\Url::to(['view', 'id_ticket' => $model->id_ticket]),
                  'title' => 'Detail Ticket ',
                  'class' => 'showModalButton text-primary',
                ]
              );
            } else {
              // Jika customer: buka halaman update (edit)
              return Html::a(
                $customerName,
                '#',
                [
                  'value' => \yii\helpers\Url::to(['update', 'id_ticket' => $model->id_ticket]),
                  'title' => 'Edit Ticket',
                  'class' => 'showModalButton text-primary',
                ]
              );
            }
          }
        ],
        [
          'header' => 'No Ticket',
          'attribute' => 'code_ticket',
        ],

        [
          'header' => 'Via',
          'attribute' => 'via',
        ],
      ];

      // Tambahkan kolom sesuai kategori
      if ($category === 'customer') {
        $columns[] = [
          'header' => 'User',
          'attribute' => 'user',
        ];
        $columns[] = [
          'header' => 'Date',
          'attribute' => 'date_ticket',
        ];
        // $columns[] = [
        //   'header' => 'Role',
        //   'attribute' => 'role',
        // ];
        $columns[] = [
          'header' => 'Modul',
          'attribute' => 'modul',
        ];
        $columns[] = [
          'header' => 'Title',
          'attribute' => 'title',
        ];
      } else { // staff
        $columns[] = [
          'header' => 'Due Date',
          'attribute' => 'duedate',
        ];
        $columns[] = [
          'header' => 'Modul',
          'attribute' => 'modul',
        ];
        $columns[] = [
          'header' => 'Label',
          'attribute' => 'label_ticket',
          'format' => 'raw',
          'value' => function ($model) {
            return '<span class="label">' . $model->label_ticket . '</span>';
          }
        ];
        $columns[] = [
          'header' => 'Priority',
          'attribute' => 'priority_ticket',
          'format' => 'raw',
          'value' => function ($model) {
            $priority = strtolower($model->priority_ticket);
            return '<span class="priority-' . $priority . '">' . $model->priority_ticket . '</span>';
          }
        ];
        $columns[] = [
          'header' => 'Status',
          'attribute' => 'status_ticket',
          'format' => 'raw',
          'value' => function ($model) {
            $status = strtolower($model->status_ticket);
            $class = 'status-' . str_replace(' ', '', $status);
            return '<span class="' . $class . '">' . $model->status_ticket . '</span>';
          }
        ];
      }

      // Tambahkan kolom aksi (view/edit/delete)

      ?>

      <!-- Render GridView -->
      <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'pjax' => true,
        'tableOptions' => ['class' => 'ticket-table table-sm'],
        'bordered' => false,
        'hover' => true,
        'columns' => $columns,
      ]); ?>



    </div>
  </div>
</div>
</div>
<?php Pjax::end(); ?>

<?php
$link_delete = Url::to(['delete']);

JSRegister::begin(['position' => View::POS_END]);
?>

<script>
  let is_fetch = false;
  let timer = null;
  let lastQuery = '';

  $(document).on('input', '#search-input', function() {
    lastQuery = $(this).val();
    clearTimeout(timer);

    if (lastQuery.length >= 3 || lastQuery.length === 0) {
      timer = setTimeout(() => {
        $.pjax.reload({
          container: '#gridDataticket',
          url: '<?= Url::to(['index', 'category' => Yii::$app->request->get('category')]) ?>' + '?TicketingSearch[queryString]=' + encodeURIComponent(lastQuery),
          replace: false,
          push: false,
          timeout: 3000,
        });
      }, 400);
    }
  });

  // Simpan nilai search input setelah reload
  $(document).on('pjax:end', function() {
    $('#search-input').val(lastQuery);
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

  $(document).on('click', '.showModalButton', function() {
    $('#modal').modal('show')
      .find('#modalContent')
      .load($(this).attr('value'));
  });

  function resetModalStyle() {
    $('#modal').find('.modal-dialog').removeAttr('style');
    $('#modal').find('.modal-content').removeAttr('style');
    $('.modal-backdrop').removeAttr('style');
  }

  $(document).on('show.bs.modal', '#modal', setModalStyle);
  $(document).on('hide.bs.modal', '#modal', resetModalStyle);

  $(document).on("click", ".delete", function(e) {
    e.preventDefault();

    const id = $(this).attr("data-id");
    if (!id) return;

    const link_delete = "<?= $link_delete; ?>?id_ticket=" + id;

    Swal.fire({
      title: 'Konfirmasi',
      text: 'Apakah Akan Menghapus Data?',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Lanjutkan',
      cancelButtonText: 'Batal',
      cancelButtonColor: '#d33',
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
</script>
<?php JSRegister::end(); ?>