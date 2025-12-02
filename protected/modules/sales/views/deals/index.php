<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\web\View;
use app\widgets\JSRegister;
use yii\widgets\ActiveForm;
use app\models\Customer;

$this->title = 'DEALS';
$this->params['breadcrumbs'][] = $this->title;

$sourceList = Customer::getSourceList();
$this->registerCssFile('https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css');
$this->registerCssFile('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.min.css');
?>

<style>
  body {
    background-color: rgba(245, 248, 250, 1) !important;
    overflow: hidden;
  }

  .deals-container {
    background-color: #ffffff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
    display: flex;
    flex-direction: column;
    height: calc(100vh - 100px);
  }

  .deals-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-shrink: 0;
  }

  .deals-title {
    color: rgba(94, 98, 120, 1);
    font-family: 'Inter Semi Bold', sans-serif;
    font-weight: 600;
    font-size: 1.5rem;
    text-transform: capitalize;
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

  .add-button:hover {
    background-color: rgba(39, 70, 94, 0.8);
  }

  /* ================= KANBAN STYLES ================= */
  .kanban-board {
    display: flex;
    flex-nowrap: nowrap;
    overflow-x: auto;
    align-items: flex-start;
    padding-bottom: 20px;
    flex-grow: 1;
    min-height: 0;
  }

  .kanban-column {
    width: 300px;
    margin-right: 15px;
  }

  .kanban-header strong {
    font-family: 'Inter Semi Bold', sans-serif;
    text-transform: uppercase;
    font-size: 1.2rem;
  }

  .kanban-header .badge {
    font-family: 'Inter Regular', sans-serif;
  }

  .kanban-header .sales-label {
    color: rgba(83, 92, 106, 1);
    font-family: 'Inter Regular', sans-serif;
    font-size: 0.8rem;
    margin-top: 2px;
    display: block;
  }

  .kanban-header .total-value {
    color: rgba(83, 92, 106, 1);
    font-family: 'Inter Regular', sans-serif;
    font-size: 1rem;
  }

  .kanban-card {
    background-color: white;
    border: 1px solid rgba(192, 191, 192, 1);
    border-radius: 5px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    margin-bottom: 10px;
    padding: 10px;
    font-family: 'Inter Regular', sans-serif;
    cursor: pointer;
  }

  .kanban-card .customer-name,
  .kanban-card .value-pair strong,
  .kanban-card .source-pair strong,
  .kanban-card .sales-person-info {
    color: rgba(83, 92, 106, 1);
  }

  .kanban-card .customer-name {
    font-size: 1.1rem;
    margin-bottom: 5px;
    font-weight: 600;
  }

  .kanban-card .sales-person-info {
    font-size: 0.9rem;
    font-weight: 500;
  }

  .kanban-card .sales-person-info .bi-person {
    font-size: 1rem;
    vertical-align: -0.15em;
    margin-right: 3px;
  }

  .kanban-card .pic-name {
    font-size: 0.9rem;
    color: rgba(83, 92, 106, 1);
    margin-bottom: 5px;
  }

  .kanban-card .date {
    font-size: 0.8rem;
    color: rgba(113, 128, 150, 1);
    margin-bottom: 5px;
  }

  .kanban-card .value-pair,
  .kanban-card .source-pair {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 3px;
    font-size: 0.8rem;
  }

  .kanban-scroll-container {
    height: 100%;
    display: flex;
    flex-direction: column;
  }

  .kanban-column-header {
    position: sticky;
    top: 0;
    z-index: 10;
    background-color: white;
    flex-shrink: 0;
    margin-bottom: 0 !important;
  }

  .kanban-cards-wrapper {
    overflow-y: auto;
    flex-grow: 1;
    padding-right: 5px;
    padding-top: 10px;
  }

  .kanban-color-bar {
    height: 5px;
    width: 100%;
    border-bottom-left-radius: 0.375rem;
    border-bottom-right-radius: 0.375rem;
  }

  .kanban-column:last-child {
    margin-right: 0;
  }

  /* === CSS UNTUK FORM MODAL (DARI QUOTATION) === */
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
yii\bootstrap5\Modal::begin([
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

<?php Pjax::begin(['id' => 'gridDatadeals', 'timeout' => false, 'enablePushState' => true, 'enableReplaceState' => false]); ?>

<div class="deals-container">
  <div class="deals-header">
    <h2 class="deals-title"><?= Html::encode($this->title) ?></h2>
    <div class="search-add-bar">
      <div class="search-input-group">
        <?php $form = ActiveForm::begin([
          'action' => ['index'],
          'method' => 'get',
          'options' => ['class' => 'd-flex align-items-center', 'data-pjax' => 1],
        ]); ?>
        <?= $form->field($searchModel, 'queryString')->textInput([
          'class' => 'search-input',
          'placeholder' => 'Search...',
        ])->label(false) ?>
        <?= Html::submitButton('<i class="bi bi-search"></i>', ['class' => 'search-button']) ?>
        <?php ActiveForm::end(); ?>
      </div>
      <?= Html::button('<i class="bi bi-plus"></i> Add', ['class' => 'add-button', 'id' => 'add-deal-button']) ?>
    </div>
  </div>

  <div class="kanban-board">
    <?php foreach ($dealsLabels as $label): ?>
      <?php
      $dealsInStatus = $dealsByLabel[$label] ?? [];
      $totalValue = array_sum(array_column($dealsInStatus, 'total'));
      $bgColor = '';
      switch ($label) {
        case 'New':
          $bgColor = 'rgba(9, 148, 153, 0.3)';
          break;
        case 'Proposal Sent':
          $bgColor = 'rgba(9, 148, 153, 0.5)';
          break;
        case 'Negotiation':
          $bgColor = 'rgba(9, 148, 153, 0.8)';
          break;
        case 'Deal Won':
          $bgColor = 'rgba(26, 161, 25, 1)';
          break;
        case 'Deal Lost':
          $bgColor = 'rgba(201, 40, 30, 0.8)';
          break;
        default:
          $bgColor = 'lightgray';
          break;
      }
      ?>
      <div class="kanban-column kanban-scroll-container">
        <div class="card shadow-sm rounded kanban-column-header" style="border: 1px solid rgba(192, 191, 192, 1); overflow: hidden;">
          <div class="card-header bg-white text-dark p-2">
            <strong><?= Html::encode(strtoupper($label)) ?></strong>
            <span class="badge bg-secondary float-end"><?= count($dealsInStatus) ?></span>
          </div>
          <div class="p-2">
            <p class="text-muted m-0 small" style="text-align: left; margin-bottom: 2px;">Sales</p>
            <p class="m-0 total-value">Rp<?= Yii::$app->formatter->asDecimal($totalValue, 0) ?></p>
          </div>
          <div class="kanban-color-bar" style="background-color: <?= Html::encode($bgColor) ?>;"></div>
        </div>

        <div class="kanban-cards-wrapper">
          <?php if (empty($dealsInStatus)): ?>
            <div class="card shadow-sm bg-light border rounded">
              <div class="card-body p-2 text-center text-muted small">No deals in this stage.</div>
            </div>
          <?php else: ?>
            <?php foreach ($dealsInStatus as $deal): ?>
              <div class="card mb-2 shadow-sm bg-white border rounded kanban-card" data-id="<?= $deal->deals_id ?>">
                <div class="card-body p-2">

                  <p class="date"><i class="bi bi-calendar-event"></i> <?= Yii::$app->formatter->asDate($deal->created_at, 'd MMM Y') ?></p>

                  <h6 class="card-title customer-name"><?= Html::encode($deal->customer->customer_name ?? 'N/A') ?></h6>

                  <p class="pic-name"><?= Html::encode($deal->customer->pic_name ?? 'N/A') ?></p>

                  <div class="value-pair">
                    <span class="text-muted">Value</span>
                    <strong>Rp<?= Yii::$app->formatter->asDecimal($deal->total, 0) ?></strong>
                  </div>

                  <div class="source-pair">
                    <span class="text-muted">Source</span>
                    <strong><?= Html::encode($sourceList[$deal->customer->customer_source] ?? $deal->customer->customer_source) ?></strong>
                  </div>

                  <div class="d-flex align-items-center mt-2 pt-2 border-top">
                    <span class="text-muted sales-person-info">
                      <i class="bi bi-person"></i>
                      <?= Html::encode($deal->createdBy->username ?? 'N/A') ?>
                    </span>
                  </div>

                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<?php Pjax::end(); ?>

<?php
// URL untuk JavaScript
$link_delete = Url::to(['delete']);
$link_create = Url::to(['create']);
$link_update = Url::to(['update']);
$link_view_modal = Url::to(['view-modal']);
?>

<?php JSRegister::begin(['position' => View::POS_END]); ?>
<script>
  $(document).ready(function() {
    function setModalTitle(title) {
      $('#modalHeader').html('<h5 class="modal-title">' + title + '</h5>');
    }

    // Add Deal button click
    $('#add-deal-button').click(function() {
      $('#modal').modal('show');
      setModalTitle('Add New Deal');
      $('#modalContent').html('<div class="text-center my-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
      $('#modalContent').load("<?= $link_create ?>");
    });

    // Ini akan memanggil actionViewModal yang menampilkan form dalam mode 'view'
    $(document).on('click', '.kanban-card', function(e) {
      if ($(e.target).closest('.btn').length) {
        return;
      }
      var dealId = $(this).data('id');
      $('#modal').modal('show');
      $('#modalHeader').html('<h5 class="modal-title">Detail Deals</h5>');
      $('#modalContent').html('<div class="text-center my-5"><div class="spinner-border text-primary"></div></div>');
      $('#modalContent').load("<?= $link_view_modal ?>?id=" + dealId);
    });
  });
</script>
<?php JSRegister::end(); ?>