<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\bootstrap5\Modal;
use yii\widgets\LinkPager;
use yii\widgets\ActiveForm;
use app\widgets\JSRegister;
use yii\web\View;

$this->title = 'CUSTOMER';
$this->params['breadcrumbs'][] = $this->title;
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.min.css">

<style>
  /* === GLOBAL & CONTAINER === */
  body {
    background-color: rgba(245, 248, 250, 1) !important;
    overflow: hidden;
  }

  .customer-container {
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

  /* === HEADER, SEARCH, ADD BUTTON === */
  .customer-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
  }

  .customer-title {
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

  /* === TABLE STYLING === */
  .customer-table-wrapper {
    overflow-y: auto;
    border: 1px solid rgba(192, 191, 192, 1);
    border-radius: 5px;
  }

  .customer-table {
    width: 100%;
    border-collapse: collapse;
    border-spacing: 0;
  }

  .customer-table th {
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

  .customer-table td {
    background-color: white;
    color: rgba(25, 25, 25, 1);
    padding: 10px;
    border-bottom: 1px solid rgba(192, 191, 192, 1);
    font-family: 'Inter Regular', sans-serif;
    cursor: pointer;
  }

  .customer-table tbody tr:hover td {
    background-color: #f5f5f5;
  }

  /* === FORM & HISTORY STYLING (DI DALAM MODAL) === */
  .contact-person-section {
    color: #00acb3;
    font-family: 'Inter Semi Bold', sans-serif;
    font-weight: 600;
    font-size: 1rem;
    margin-top: 15px;
  }

  .contact-person-divider {
    border-bottom: 1px solid rgba(96, 100, 122, 0.3);
    margin-top: 5px;
    margin-bottom: 10px;
  }

  .form-footer {
    border-top: 1px solid #e9ecef;
  }

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

  /* === SWEETALERT STYLING (YANG HILANG) === */
  .swal2-popup.swal2-noicon {
    padding-top: 2.5em;
  }

  .swal2-popup.swal2-noicon .swal2-icon {
    display: none;
  }

  .swal2-actions {
    gap: 1rem !important;
  }

  .swal2-styled.swal2-cancel.btn-outline-custom {
    background-color: transparent !important;
    color: #27465E !important;
    border: 1px solid #27465E !important;
    box-shadow: none !important;
  }

  .swal2-styled.swal2-cancel.btn-outline-custom:hover {
    background-color: rgba(39, 70, 94, 0.05) !important;
  }
</style>

<?php
Modal::begin([
  'headerOptions' => ['id' => 'modalHeader'],
  'id' => 'modal',
  'size' => 'modal-xl',
  'options' => ['data-bs-backdrop' => 'static', 'data-bs-keyboard' => 'false', 'tabindex' => false, 'class' => 'fade'],
]);
echo "<div id='modalContent'><div class='text-center p-5'><div class='spinner-border'></div></div></div>";
Modal::end();
?>

<?php Pjax::begin(['id' => 'grid-customer-pjax']); ?>
<div class="customer-container">
  <div class="customer-header">
    <h2 class="customer-title"><?= Html::encode($this->title) ?></h2>
    <div class="search-add-bar">
      <div class="search-input-group">
        <?php $form = ActiveForm::begin(['action' => ['index'], 'method' => 'get', 'options' => ['class' => 'd-flex align-items-center', 'data-pjax' => 1]]); ?>
        <?= $form->field($searchModel, 'queryString')->textInput(['class' => 'search-input', 'placeholder' => 'Search...'])->label(false) ?>
        <?= Html::submitButton('<i class="bi bi-search"></i>', ['class' => 'search-button']) ?>
        <?php ActiveForm::end(); ?>
      </div>
      <?= Html::button('<i class="bi bi-plus"></i> Add', ['class' => 'add-button', 'id' => 'add-customer-button']) ?>
    </div>
  </div>

  <div class="customer-table-wrapper">
    <table class="customer-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Code</th>
          <th>Name</th>
          <th>Email</th>
          <th>Phone</th>
          <th>Personal Contact</th>
          <th>Salesperson</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($dataProvider->getModels() as $index => $model): ?>
          <tr data-customer-id="<?= $model->customer_id ?>">
            <td><?= $index + 1 + $dataProvider->pagination->offset ?></td>
            <td><?= Html::encode($model->customer_code) ?></td>
            <td><?= Html::encode($model->customer_name) ?></td>
            <td><?= Html::encode($model->customer_email) ?></td>
            <td><?= Html::encode($model->customer_phone) ?></td>
            <td><?= Html::encode($model->pic_name) ?></td>
            <td><?= Html::encode($model->createdBy->username ?? '(not set)') ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="mt-3 d-flex justify-content-center">
    <?= LinkPager::widget(['pagination' => $dataProvider->pagination]); ?>
  </div>
</div>
<?php Pjax::end(); ?>


<?php JSRegister::begin(['position' => View::POS_END]); ?>
<script>
  $(document).ready(function() {
    function setModalTitle(title) {
      $('#modalHeader').html('<h5 class="modal-title">' + title + '</h5>');
    }

    $('#add-customer-button').click(function() {
      $('#modal').modal('show');
      setModalTitle('Add New Customer');
      $('#modalContent').html('<div class="text-center my-5"><div class="spinner-border text-primary"></div></div>');
      $('#modalContent').load('<?= Url::to(['/sales/customer/create']) ?>');
    });

    $(document).on('click', '.customer-table tbody tr', function() {
      const customerId = $(this).data('customer-id');
      if (!customerId) return;
      $('#modal').modal('show');
      setModalTitle('Customer Details');
      $('#modalContent').html('<div class="text-center my-5"><div class="spinner-border text-primary"></div></div>');
      $('#modalContent').load('<?= Url::to(['/sales/customer/view-modal']) ?>?customer_id=' + customerId);
    });
  });
</script>
<?php JSRegister::end(); ?>