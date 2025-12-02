<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\web\View;
use app\widgets\JSRegister;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\sales\QuotationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'QUOTATION';
$this->params['breadcrumbs'][] = $this->title;

?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.min.css">

<style>
  body {
    background-color: rgba(245, 248, 250, 1) !important;
    overflow: hidden;
  }

  .quotation-container {
    background-color: #ffffff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
    display: flex;
    flex-direction: column;
    height: calc(100vh - 100px);
    overflow-y: auto;
  }

  .quotation-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
  }

  .quotation-title {
    color: rgba(94, 98, 120, 1);
    font-family: 'Inter Semi Bold', sans-serif;
    font-weight: 600;
    font-size: 1.5rem;
  }

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
  }

  .search-input {
    border: none;
    background-color: transparent;
    padding: 15px 1px 2px 8px;
    flex-grow: 1;
    font-family: 'Inter Regular', sans-serif;
    font-size: 0.9rem;
  }

  .search-button {
    background-color: transparent;
    border: none;
    padding: 5px 8px;
  }

  .search-button i {
    color: rgba(72, 129, 173, 1);
  }

  .add-button {
    background-color: rgba(39, 70, 94, 1);
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 5px;
    font-family: 'Inter Regular', sans-serif;
    font-size: 0.9rem;
    height: 30px;
  }

  .quotation-table-wrapper {
    overflow-y: auto;
    border: 1px solid rgba(192, 191, 192, 1);
    border-radius: 5px;
  }

  .quotation-table {
    width: 100%;
    border-collapse: collapse;
    border-spacing: 0;
  }

  .quotation-table th {
    background-color: rgba(229, 231, 235, 1);
    color: rgba(63, 66, 84, 1);
    padding: 10px;
    text-align: left;
    border-bottom: 1px solid rgba(192, 191, 192, 1);
    font-family: 'Inter Semi Bold', sans-serif;
    font-weight: 600;
    position: sticky;
    top: 0;
    z-index: 1;
  }

  .quotation-table td {
    background-color: white;
    color: rgba(25, 25, 25, 1);
    padding: 10px;
    border-bottom: 1px solid rgba(192, 191, 192, 1);
    font-family: 'Inter Regular', sans-serif;
    cursor: pointer;
  }

  .status-sent {
    color: #1AA119;
    background-color: rgba(26, 161, 25, 0.15);
    padding: 2px 6px;
    border-radius: 4px;
    font-weight: 600;
  }

  .status-process {
    color: #4881AD;
    background-color: rgba(108, 179, 234, 0.15);
    padding: 2px 6px;
    border-radius: 4px;
    font-weight: 600;
  }

  .form-section-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #4a5568;
    margin-top: 1.5rem;
    margin-bottom: 1rem;
    border-bottom: 1px solid #e2e8f0;
    padding-bottom: 0.5rem;
  }

  .product-table-wrapper {
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    overflow: hidden;
    margin-bottom: 1rem;
  }

  .product-table {
    width: 100%;
    border-collapse: collapse;
  }

  .product-table th,
  .product-table td {
    padding: 0.75rem;
    vertical-align: top;
    text-align: left;
    border-bottom: 1px solid #e2e8f0;
  }

  .product-table tr:last-child td {
    border-bottom: none;
  }

  .product-table th {
    background-color: #f8f9fa;
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .product-table td .form-group {
    margin-bottom: 0;
  }

  .form-footer {
    border-top: 1px solid #e9ecef;
    padding-top: 1rem;
    margin-top: 1.5rem;
  }

  .btn-delete-icon {
    background-color: transparent;
    border: 1px solid transparent;
    color: #dc3545;
    font-size: 1.25rem;
    padding: 0.375rem 0.75rem;
    border-radius: 0.375rem;
    line-height: 1;
  }

  .btn-delete-icon:hover {
    background-color: #f8d7da;
    border-color: #f5c2c7;
  }

  .product-tabs {
    display: flex;
    border-bottom: 2px solid #e2e8f0;
    margin-bottom: 1.5rem;
  }

  .product-tab {
    padding: 0.75rem 1rem;
    cursor: pointer;
    font-weight: 500;
    color: #4a5568;
    position: relative;
    border: none;
    background: none;
  }

  .product-tab.active {
    color: #27465e;
    font-weight: 600;
  }

  .product-tab.active::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    right: 0;
    height: 2px;
    background-color: #27465e;
  }

  .product-fields-container {
    display: none;
  }

  .product-fields-container.active {
    display: block;
  }

  /* Menghilangkan icon dan menyesuaikan padding atas pada SweetAlert */
  .swal2-popup.swal2-noicon {
    padding-top: 2.5em;
  }

  .swal2-popup.swal2-noicon .swal2-icon {
    display: none;
  }

  /* Memberi jarak antar tombol di SweetAlert */
  .swal2-actions {
    gap: 1rem !important;
  }

  /* Style untuk tombol Cancel dengan outline */
  .swal2-styled.swal2-cancel.btn-outline-custom {
    background-color: transparent !important;
    color: #27465E !important;
    border: 1px solid #27465E !important;
    box-shadow: none !important;
  }

  .swal2-styled.swal2-cancel.btn-outline-custom:hover {
    background-color: rgba(39, 70, 94, 0.05) !important;
  }

  /* Style untuk tombol-tombol di footer form */
  .btn-custom-primary {
    background-color: #27465E !important;
    border-color: #27465E !important;
    color: white !important;
  }

  .btn-custom-primary:hover {
    background-color: #1a2e3c !important;
    border-color: #1a2e3c !important;
  }

  .btn-custom-secondary {
    background-color: #D9D9D9 !important;
    border-color: #D9D9D9 !important;
    color: #343a40 !important;
  }

  .btn-custom-secondary:hover {
    background-color: #c2c2c2 !important;
    border-color: #c2c2c2 !important;
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
    'class' => 'fade'
  ],
]);
echo "<div id='modalContent'></div>";
yii\bootstrap5\Modal::end();
?>

<?php Pjax::begin(['id' => 'gridDataquotation', 'timeout' => false, 'enablePushState' => true, 'enableReplaceState' => false]); ?>
<div class="quotation-container">
  <div class="quotation-header">
    <h2 class="quotation-title"><?= Html::encode($this->title) ?></h2>
    <div class="search-add-bar">
      <div class="search-input-group">
        <?php
        $form = ActiveForm::begin([
          'action' => ['index'],
          'method' => 'get',
          'options' => [
            'data-pjax' => 1,
            'class' => 'd-flex align-items-center'
          ],
        ]); ?>
        <?= $form->field($searchModel, 'queryString')->textInput([
          'class' => 'search-input',
          'placeholder' => 'Search ...',
        ])->label(false) ?>
        <?= Html::submitButton('<i class="bi bi-search"></i>', ['class' => 'search-button']) ?>
        <?php ActiveForm::end(); ?>
      </div>
      <?= Html::button('<i class="bi bi-plus"></i> Add', ['class' => 'add-button', 'id' => 'add-quotation-button']) ?>
    </div>
  </div>

  <!-- ... HTML untuk tabel ... -->
  <div class="quotation-table-wrapper">
    <table class="quotation-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Code</th>
          <th>Created Date</th>
          <th>Customer</th>
          <th>Product</th>
          <th>Salesperson</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($dataProvider->getCount() > 0): ?>
          <?php foreach ($dataProvider->getModels() as $index => $model): ?>
            <tr data-quotation-id="<?= $model->quotation_id ?>">
              <td><?= $index + 1 + $dataProvider->pagination->offset ?></td>
              <td><?= Html::encode($model->quotation_code) ?></td>
              <td><?= Yii::$app->formatter->asDate($model->created_date, 'php:d-m-Y') ?></td>
              <td><?= Html::encode($model->customer->customer_name ?? '-') ?></td>
              <td><?= Html::encode($model->product->product_name ?? '-') ?></td>
              <td><?= Html::encode($model->activeDeal->createdBy->username ?? '(not set)') ?></td>
              <td>
                <?php
                $statusClass = '';
                if (strtolower($model->quotation_status) === 'sent') {
                  $statusClass = 'status-sent';
                } elseif (strtolower($model->quotation_status) === 'process') {
                  $statusClass = 'status-process';
                }
                echo Html::tag('span', Html::encode($model->quotation_status), ['class' => $statusClass]);
                ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="6" class="text-center">No data found.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php
  echo \yii\widgets\LinkPager::widget([
    'pagination' => $dataProvider->pagination,
    'options' => ['class' => 'pagination justify-content-center mt-3'],
    'linkContainerOptions' => ['class' => 'page-item'],
    'linkOptions' => ['class' => 'page-link'],
    'disabledListItemSubTagOptions' => ['tag' => 'a', 'class' => 'page-link'],
  ]);
  ?>
</div>

<?php Pjax::end(); ?>

<?php
JSRegister::begin(['position' => View::POS_END]);
?>
<script>
  $(document).ready(function() {
    // Fungsi untuk set judul modal, mengganti judul lama
    function setModalTitle(title) {
      $('#modalHeader').html('<h5 class="modal-title">' + title + '</h5>');
    }

    // Add Quotation
    $('#add-quotation-button').click(function() {
      $('#modal').modal('show');
      setModalTitle('Add Quotation');
      $('#modalContent').html('<div class="text-center my-5"><div class="spinner-border text-primary"></div></div>');
      $('#modalContent').load("<?= Url::to(['/sales/quotation/create']) ?>", function(response, status, xhr) {
        if (status == "error") {
          $('#modalContent').html('<div class="alert alert-danger">Failed to load form.</div>');
        }
      });
    });

    // Klik baris untuk buka detail quotation
    $(document).on('click', '.quotation-table tbody tr', function() {
      const quotationId = $(this).data('quotation-id');
      if (!quotationId) return;

      $('#modal').modal('show');
      $('#modalHeader').html('<h5 class="modal-title">Detail Quotation</h5>');
      $('#modalContent').html('<div class="text-center my-5"><div class="spinner-border text-primary"></div></div>');
      $('#modalContent').load("/sales/quotation/view?quotation_id=" + quotationId, function(response, status) {
        if (status === "error") {
          $('#modalContent').html('<div class="alert alert-danger">Failed to load data.</div>');
        }
      });
    });
  });
</script>
<?php JSRegister::end(); ?>