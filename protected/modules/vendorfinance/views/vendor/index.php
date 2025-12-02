<?php
$this->registerCss("
  .filter-buttons {
    display: flex;
    gap: 10px;
    align-items: center;
  }
");
?>
<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\grid\ActionColumn;
use yii\web\View;
use app\widgets\JSRegister;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\vendorfinance\VendorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Vendor';
$this->params['breadcrumbs'][] = $this->title;

?>
<style>
  .btn.btn-primary {
    background-color: #27465E;
    border-color: #27465E;
  }

  .btn.btn-primary:hover {
    background-color: #1f384d;
    /* warna sedikit lebih gelap untuk hover */
    border-color: #1f384d;
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

  /* CSS untuk GridView */
  .table-sm {
    background-color: #FFFFFF;
    /* Warna latar belakang tabel */
    border: 1px solid #C0BFC0;
    /* Warna border tabel */
  }

  .table-sm th {
    background-color: #E5E7EB;
    /* Warna latar belakang header */
    color: #3F4254;
    /* Warna tulisan header */
    border: 1px solid #C0BFC0;
    /* Warna border header */
  }

  .table-sm td {
    background-color: #FFFFFF;
    /* Warna latar belakang sel */
    color: #191919;
    /* Warna tulisan sel */
    border: 1px solid #C0BFC0;
    /* Warna border sel */
  }

  .table-sm tr:nth-child(even) td {
    background-color: #f8f9fa;
    /* Warna baris genap */
  }

  .table-sm tr:nth-child(odd) td {
    background-color: #ffffff;
    /* Warna baris ganjil */
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
    'options' => ['tabindex' => false],
  ],
]);
echo "<div id='modalContent'></div>";
yii\bootstrap5\Modal::end();
?>

<div class="card">
  <div class="card-header d-flex align-items-center">
    <?php if ($filter === 'vendor'): ?>
      <div class="header-title flex-grow-1">
        <h4 class="card-title">Vendor</h4>
      </div>
    <?php endif; ?>
    <?php if ($filter === 'staff'): ?>
      <div class="header-title flex-grow-1">
        <h4 class="card-title">Staff</h4>
      </div>
    <?php endif; ?>

    <div style="width:20%">
      <?= Html::input('text', 'queryString', Yii::$app->request->get('queryString'), [
        'id' => 'global-search',
        'class' => 'form-control',
        'placeholder' => 'Cari data ' . $filter,
        'data-filter' => $filter // penting untuk identifikasi
      ]) ?>
    </div>

    <?php if ($filter === 'vendor'): ?>
      <div class="ms-3">
        <?php // echoHtml::a('<i class="fa fa-search"></i> Cari', '#vendor-search', ['class' => 'btn btn-sm btn-warning', 'data-bs-toggle' => 'collapse' , 'aria-controls' => 'vendor-search']); 
        ?>
        <?= Html::button(
          ' <i class="fas fa-plus"></i> Add',
          ['value' => Url::to(['create']), 'title' => 'Form ' . 'Vendor', 'class' => 'showModalButton btn btn-primary']
        ); ?>
      </div>
    <?php endif; ?>
  </div>
  <div class="filter-buttons ms-3 d-flex gap-2">
    <?= Html::a(
      '<i class="fas fa-user"></i> Vendor',
      ['vendor/index', 'filter' => 'vendor'],
      [
        'class' => $filter === 'vendor' ? 'btn btn-primary' : 'btn btn-secondary',
        'encode' => false, // penting agar icon tidak di-escape
      ]
    ) ?>
    <?= Html::a(
      '<i class="fas fa-user-tie"></i> Staff',
      ['vendor/index', 'filter' => 'staff'],
      [
        'class' => $filter === 'staff' ? 'btn btn-primary' : 'btn btn-secondary',
        'encode' => false,
      ]
    ) ?>
  </div>


  <div class="card-body">
    <div class="vendor-index col-lg-12 p-0 ">
      <?php Pjax::begin(['id' => 'gridDatavendor', 'timeout' => false, 'enablePushState' => true, 'enableReplaceState' => false]); ?>
      <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'pjax' => true,
        'filterModel' => null,
        'tableOptions' => ['class' => 'table-sm '],
        'bordered' => false,
        'hover' => true,
        'columns' => $filter === 'vendor' ? [
          ['class' => 'yii\grid\SerialColumn'],
          'nama_vendor',
          'alamat_vendor',
          'email_vendor',
          'telp_vendor',
          'nama_PIC',
          'email_PIC:email',
          'telp_PIC',
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
        ', '#', ['value' => Url::to(['view', 'id_vendor' => $model->id_vendor]), 'title' => 'Detail ' . 'Vendor', 'class' => 'showModalButton text-primary me-md-1']) .
                Html::a('
        <svg class="icon-16" width="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M13.7476 20.4428H21.0002" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
          <path fill-rule="evenodd" clip-rule="evenodd" d="M12.78 3.79479C13.5557 2.86779 14.95 2.73186 15.8962 3.49173C15.9485 3.53296 17.6295 4.83879 17.6295 4.83879C18.669 5.46719 18.992 6.80311 18.3494 7.82259C18.3153 7.87718 8.81195 19.7645 8.81195 19.7645C8.49578 20.1589 8.01583 20.3918 7.50291 20.3973L3.86353 20.443L3.04353 16.9723C2.92866 16.4843 3.04353 15.9718 3.3597 15.5773L12.78 3.79479Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
          <path d="M11.021 6.00098L16.4732 10.1881" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
        </svg>
        ', '#', ['value' => Url::to(['update', 'id_vendor' => $model->id_vendor]), 'title' => 'Form Edit ' . 'Vendor', 'class' => 'showModalButton text-success me-md-1']) .
                Html::a('
        <svg class="icon-16" width="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M19.3248 9.46826C19.3248 9.46826 18.7818 16.2033 18.4668 19.0403C18.3168 20.3953 17.4798 21.1893 16.1088 21.2143C13.4998 21.2613 10.8878 21.2643 8.27979 21.2093C6.96079 21.1823 6.13779 20.3783 5.99079 19.0473C5.67379 16.1853 5.13379 9.46826 5.13379 9.46826" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
          <path d="M20.708 6.23975H3.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
          <path d="M17.4406 6.23973C16.6556 6.23973 15.9796 5.68473 15.8256 4.91573L15.5826 3.69973C15.4326 3.13873 14.9246 2.75073 14.3456 2.75073H10.1126C9.53358 2.75073 9.02558 3.13873 8.87558 3.69973L8.63258 4.91573C8.47858 5.68473 7.80258 6.23973 7.01758 6.23973" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
        </svg>
        ', '#', ['id_vendor' => $model->id_vendor, 'title' => 'Hapus Data?', 'class' => 'text-danger delete']);
            }
          ],
        ] : [
          ['class' => 'yii\grid\SerialColumn'],
          'nama_lengkap',
          'alamat',
          'email',
          'jenis_pegawai',
          'no_hp',
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
  $(document).on('click', '.showModalButton', function () {
  const url = $(this).attr('value');
  const title = $(this).attr('title') || 'Detail';

  $('#modal').modal('show')
    .find('#modalContent')
    .html('<div class="p-3 text-center"><div class="spinner-border text-primary" role="status"></div></div>')
    .load(url, function (response, status, xhr) {
      if (status === "error") {
        $(this).html('<div class="text-danger p-3">Gagal memuat data.</div>');
      }
    });

  $('#modalHeader').text(title);
});


  var is_fetch = false;
  $(document).on("click", ".delete", function() {
    var id = $(this).attr("id_vendor");
    var link_delete = "<?= $link_delete; ?>?id_vendor=" + id;
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
                  container: "#gridDatavendor"
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
  $(document).on('keyup', '#global-search', function(e) {
    let query = $(this).val();
    let filter = $(this).data('filter');
    let url = new URL(window.location.href);
    let params = new URLSearchParams();

    params.set('queryString', query);
    params.set('filter', filter);

    $.pjax.reload({
      container: '#gridDatavendor',
      url: url.pathname + '?' + params.toString(),
      timeout: false
    });
  });
  $(document).on('pjax:end', function() {
    if (window.history.replaceState) {
      const cleanUrl = window.location.origin + window.location.pathname + window.location.search.replace(/([&?])queryString=[^&]+(&|$)/, '$1').replace(/[\?&]$/, '');
      window.history.replaceState(null, null, cleanUrl);
    }
  });
  $(document).ready(function() {
    $('#global-search').val('');
  });
</script>
<?php JSRegister::end(); ?>