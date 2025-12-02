<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use app\widgets\JSRegister;
use yii\web\View;

$this->title = Yii::t('app', 'Account Keluar');
$this->params['breadcrumbs'][] = $this->title;
?>

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

<?php Pjax::begin(['id' => 'gridDataaccount-keluar', 'timeout' => false, 'enablePushState' => true, 'enableReplaceState' => false]); ?>
<div class="card">
  <div class="card-header d-flex">
    <div class="header-title flex-grow-1">
      <h4 class="card-title">Account Keluar</h4>
    </div>
    <div style="width:20%">
      <?= $this->render('_search', ['model' => $searchModel]); ?>
    </div>
    <div class="ms-3">
      <?= Html::button('Tambah', [
        'value' => Url::to(['create']),
        'title' => 'Form ' . Yii::t('app', 'Account Keluar'),
        'class' => 'showModalButton btn btn-primary'
      ]); ?>
    </div>
  </div>
  <div class="card-body">
    <div class="account-keluar-index col-lg-12 p-0 table-responsive">
      <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'pjax' => true,
        'tableOptions' => ['class' => 'table-sm'],
        'bordered' => false,
        'hover' => true,
        'columns' => [
          ['class' => 'yii\grid\SerialColumn'],
          [
            'header' => '',
            'format' => 'raw',
            'value' => function ($model) {
              if ($model->parent_id === null) {
                return '<a href="#" class="toggle-child" data-id="' . $model->id . '">+</a>';
              }
              return '';
            },
            'contentOptions' => ['style' => 'width:20px']
          ],
          'code',
          'akun',
          'penggunaan',
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
        ', '#', ['value' => Url::to(['view', 'id' => $model->id]), 'title' => 'Detail ' . Yii::t('app', 'Account Keluar'), 'class' => 'showModalButton text-primary me-md-1']) .
                Html::a('
        <svg class="icon-16" width="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M13.7476 20.4428H21.0002" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
          <path fill-rule="evenodd" clip-rule="evenodd" d="M12.78 3.79479C13.5557 2.86779 14.95 2.73186 15.8962 3.49173C15.9485 3.53296 17.6295 4.83879 17.6295 4.83879C18.669 5.46719 18.992 6.80311 18.3494 7.82259C18.3153 7.87718 8.81195 19.7645 8.81195 19.7645C8.49578 20.1589 8.01583 20.3918 7.50291 20.3973L3.86353 20.443L3.04353 16.9723C2.92866 16.4843 3.04353 15.9718 3.3597 15.5773L12.78 3.79479Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
          <path d="M11.021 6.00098L16.4732 10.1881" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
        </svg>
        ', '#', ['value' => Url::to(['update', 'id' => $model->id]), 'title' => 'Form Edit ' . Yii::t('app', 'Account Keluar'), 'class' => 'showModalButton text-success me-md-1']) .
                Html::a('
        <svg class="icon-16" width="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M19.3248 9.46826C19.3248 9.46826 18.7818 16.2033 18.4668 19.0403C18.3168 20.3953 17.4798 21.1893 16.1088 21.2143C13.4998 21.2613 10.8878 21.2643 8.27979 21.2093C6.96079 21.1823 6.13779 20.3783 5.99079 19.0473C5.67379 16.1853 5.13379 9.46826 5.13379 9.46826" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
          <path d="M20.708 6.23975H3.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
          <path d="M17.4406 6.23973C16.6556 6.23973 15.9796 5.68473 15.8256 4.91573L15.5826 3.69973C15.4326 3.13873 14.9246 2.75073 14.3456 2.75073H10.1126C9.53358 2.75073 9.02558 3.13873 8.87558 3.69973L8.63258 4.91573C8.47858 5.68473 7.80258 6.23973 7.01758 6.23973" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
        </svg>
        ', '#', ['id' => $model->id, 'title' => 'Hapus Data?', 'class' => 'text-danger delete']);
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
$link_child = Url::to(['child-akun']);

JSRegister::begin(['position' => View::POS_END]);
?>
<script>
  var is_fetch = false;

  $(document).on("click", ".delete", function() {
    var id = $(this).attr("id");
    var link_delete = "<?= $link_delete; ?>?id=" + id;
    Swal.fire({
      title: 'Konfirmasi',
      text: 'Apakah Akan Menghapus Data?',
      showCancelButton: true,
      confirmButtonText: 'Lanjutkan',
      cancelButtonText: 'Batal',
      icon: 'question'
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
                container: "#gridDataaccount-keluar"
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
  $(document).on('click', '.showModalButton', function () {
  $('#modal').modal('show')
    .find('#modalContent')
    .load($(this).attr('value'));
});
  $(document).on('click', '.toggle-child', function(e) {
    e.preventDefault();
    var parentId = $(this).data('id');
    var row = $(this).closest('tr');

    if ($(this).text() === '+') {
      $(this).text('-');
      $.get('<?= $link_child ?>', {
        parent_id: parentId
      }, function(data) {
        row.after(data);
      });
    } else {
      $(this).text('+');
      row.siblings('.child-of-' + parentId).remove();
    }
  });
</script>
<?php JSRegister::end(); ?>