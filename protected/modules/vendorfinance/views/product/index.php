<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\grid\ActionColumn;
use yii\web\View;
use app\widgets\JSRegister;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\vendorfinance\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Product');
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
  body {
    background-color: rgba(245, 248, 250, 1) !important;
    overflow: hidden;
  }

  .btn.btn-primary {
    background-color: #27465E;
    border-color: #27465E;
  }

  .btn.btn-primary:hover {
    background-color: #1f384d;
    border-color: #1f384d;
  }

  .btn-secondary {
    background-color: transparent !important;
    border: 1px solid #E1E3EA !important;
    color: #5E6278 !important;
  }

  .btn-secondary:hover {
    background-color: rgba(225, 227, 238, 0.1);
    border-color: #E1E3EA;
    color: #5E6278;
  }

  .btn-light-custom {
    background-color: #E5E7EB;
    border: 1px solid #C0BFC0;
    color: #191919;
  }

  .btn-light-custom:hover {
    background-color: #D1D4D9;
    border-color: #A3A9B3;
  }

  .table-sm {
    background-color: #FFFFFF;
    border: 1px solid #C0BFC0;
  }

  .table-sm th {
    background-color: #E5E7EB;
    color: #3F4254;
    border: 1px solid #C0BFC0;
  }

  .table-sm td {
    background-color: #FFFFFF;
    color: #191919;
    border: 1px solid #C0BFC0;
  }

  .table-sm tr:nth-child(even) td {
    background-color: #f8f9fa;
  }

  .table-sm tr:nth-child(odd) td {
    background-color: #ffffff;
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

<div class="card">
  <div class="card-header d-flex">
    <div class="header-title flex-grow-1">
      <h4 class="card-title">PRODUCT</h4>
    </div>
    <div style="width:20%">
      <?= $this->render('_search', ['model' => $searchModel]); ?>
    </div>
    <div class="ms-3">
      <?= Html::button('Tambah |
      <svg class="icon-16" width="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd" clip-rule="evenodd" d="M14.7366 2.76175H8.08455C6.00455 2.75375 4.29955 4.41075 4.25055 6.49075V17.3397C4.21555 19.3897 5.84855 21.0807 7.89955 21.1167C7.96055 21.1167 8.02255 21.1167 8.08455 21.1147H16.0726C18.1416 21.0937 19.8056 19.4087 19.8026 17.3397V8.03975L14.7366 2.76175Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
        <path d="M14.4741 2.75V5.659C14.4741 7.079 15.6231 8.23 17.0431 8.234H19.7971" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
        <path d="M14.2936 12.9141H9.39355" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
        <path d="M11.8442 15.3639V10.4639" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
      </svg>', ['value' => Url::to(['create']), 'title' => 'Form ' . Yii::t('app', 'Product'), 'class' => 'showModalButton btn btn-primary']); ?>
    </div>
  </div>

  <div class="card-body">
    <div class="product-index col-lg-12 p-0 table-responsive">
      <?php Pjax::begin(['id' => 'gridDataproduct', 'timeout' => false, 'enablePushState' => true, 'enableReplaceState' => false]); ?>
      <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'pjax' => true,
        'tableOptions' => ['class' => 'table-sm'],
        'bordered' => false,
        'hover' => true,
        'columns' => [
          ['class' => 'yii\grid\SerialColumn'],
          'code_produk',
          'keterangan',
          'unit',
          'product_name',
          [
            'attribute' => 'harga',
            'value' => fn($model) =>
            $model->harga !== null ? 'Rp ' . number_format((float)$model->harga, 0, ',', '.') : 'Rp 0',
            'format' => 'raw',
            'contentOptions' => ['style' => 'text-align: right;'],
          ],
          [
            'header' => 'Actions',
            'format' => 'raw',
            'hAlign' => 'right',
            'value' => function ($model) {
              return Html::a('<svg class="icon-16" width="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <circle cx="11.7669" cy="11.7666" r="8.98856" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></circle>
          <path d="M18.0186 18.4851L21.5426 22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>', '#', [
                'value' => Url::to(['view', 'id_produk' => $model->id_produk]),
                'title' => 'Detail Product',
                'class' => 'showModalButton text-primary me-md-1'
              ]) .
                Html::a(' <svg class="icon-16" width="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M13.7476 20.4428H21.0002" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
          <path fill-rule="evenodd" clip-rule="evenodd" d="M12.78 3.79479C13.5557 2.86779 14.95 2.73186 15.8962 3.49173C15.9485 3.53296 17.6295 4.83879 17.6295 4.83879C18.669 5.46719 18.992 6.80311 18.3494 7.82259C18.3153 7.87718 8.81195 19.7645 8.81195 19.7645C8.49578 20.1589 8.01583 20.3918 7.50291 20.3973L3.86353 20.443L3.04353 16.9723C2.92866 16.4843 3.04353 15.9718 3.3597 15.5773L12.78 3.79479Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
          <path d="M11.021 6.00098L16.4732 10.1881" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
        </svg>', '#', [
                  'value' => Url::to(['update', 'id_produk' => $model->id_produk]),
                  'title' => 'Form Edit Product',
                  'class' => 'showModalButton text-success me-md-1'
                ]) .
                Html::a('<svg class="icon-16" width="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M19.3248 9.46826C19.3248 9.46826 18.7818 16.2033 18.4668 19.0403C18.3168 20.3953 17.4798 21.1893 16.1088 21.2143C13.4998 21.2613 10.8878 21.2643 8.27979 21.2093C6.96079 21.1823 6.13779 20.3783 5.99079 19.0473C5.67379 16.1853 5.13379 9.46826 5.13379 9.46826" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
          <path d="M20.708 6.23975H3.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
          <path d="M17.4406 6.23973C16.6556 6.23973 15.9796 5.68473 15.8256 4.91573L15.5826 3.69973C15.4326 3.13873 14.9246 2.75073 14.3456 2.75073H10.1126C9.53358 2.75073 9.02558 3.13873 8.87558 3.69973L8.63258 4.91573C8.47858 5.68473 7.80258 6.23973 7.01758 6.23973" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
        </svg>', '#', [
                  'id_produk' => $model->id_produk,
                  'title' => 'Hapus Data?',
                  'class' => 'text-danger delete'
                ]);
            }
          ],
        ],
      ]); ?>
      <?php Pjax::end(); ?>
    </div>
  </div>
</div>

<?php
$link_delete = Url::to(['delete']);
JSRegister::begin(['position' => View::POS_END]);
?>
<script>
  let is_fetch = false;
  let timer = null;
  let lastQuery = '';

  $(document).on('input', '#search-input', function () {
    lastQuery = $(this).val();
    clearTimeout(timer);
    if (lastQuery.length >= 3 || lastQuery.length === 0) {
      timer = setTimeout(() => {
        $.pjax.reload({
          container: '#gridDataproduct',
          url: '<?= Url::to(['index']) ?>?ProductSearch[queryString]=' + encodeURIComponent(lastQuery),
          replace: false,
          push: false,
          timeout: 1000,
        });
      }, 400);
    }
  });

  $(document).on('pjax:end', function () {
    $('#search-input').val(lastQuery);
    $('#search-input').prop('disabled', false);
  });

  $(document).on("click", ".delete", function () {
    const id = $(this).attr("id_produk");
    const link_delete = "<?= $link_delete ?>?id_produk=" + id;

    Swal.fire({
      title: 'Konfirmasi',
      text: 'Apakah Akan Menghapus Data?',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Lanjutkan',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed && !is_fetch) {
        is_fetch = true;

        // ✅ Gunakan dataType: 'json' agar jQuery otomatis handle response JSON
        $.ajax({
          url: link_delete,
          type: 'POST',
          dataType: 'json',
          success: function (response) {
            is_fetch = false;
            if (response.status === 'success') {
              Swal.fire({
                title: 'Berhasil',
                text: response.message,
                icon: 'success'
              }).then(() => {
                location.reload(); // ✅ reload full page
              });
            } else {
              Swal.fire('Gagal', response.message, 'warning');
            }
          },
          error: function () {
            is_fetch = false;
            Swal.fire('Error', 'Terjadi kesalahan saat menghapus data.', 'error');
          }
        });
      }
    });
  });
</script>
<?php JSRegister::end(); ?>
