<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\web\View;
use app\widgets\JSRegister;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;

$this->title = 'LEGAL CONTRACTS';
$this->params['breadcrumbs'][] = $this->title;
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.min.css">

<style>
  body {
    background-color: #f5f8fa !important;
    overflow: hidden;
  }

  .contract-container {
    background-color: #ffffff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    height: calc(100vh - 100px);
    overflow-y: auto;
    display: flex;
    flex-direction: column;
  }

  .contract-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
  }

  .contract-title {
    color: #5e6278;
    font-weight: 600;
    font-size: 1.5rem;
  }

  .search-add-bar {
    display: flex;
    gap: 10px;
  }

  .search-input-group {
    background-color: #e5e7eb;
    border: 1px solid #c0bfc0;
    border-radius: 5px;
    display: flex;
    align-items: center;
    height: 30px;
  }

  .search-input {
    border: none;
    background: transparent;
    padding: 15px 1px 2px 8px;
    flex-grow: 1;
    font-size: 0.9rem;
  }

  .search-button {
    background: transparent;
    border: none;
    padding: 4px 8px;
    font-size: 0.9rem;
    border-left: 1px solid #c0bfc0;
    color: #4881ad;
  }

  .add-button {
    background-color: #27465e;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 5px;
    font-size: 0.9rem;
    height: 30px;
  }

  .add-button:hover {
    background-color: rgba(39, 70, 94, 0.8);
  }

  .contract-table-wrapper {
    overflow-y: auto;
    border: 1px solid #c0bfc0;
    border-radius: 5px;
  }

  .contract-table {
    width: 100%;
    border-collapse: collapse;
  }

  .contract-table th {
    background-color: #e5e7eb;
    color: #3f4254;
    padding: 10px;
    text-align: left;
    font-weight: 600;
    position: sticky;
    top: 0;
    z-index: 1;
  }

  .contract-table td {
    background: white;
    color: #191919;
    padding: 10px;
    border-bottom: 1px solid #c0bfc0;
    cursor: pointer;
  }

  .contract-table tbody tr:hover td {
    background-color: #f5f5f5;
  }

  .status-active {
    color: #1aa119;
    background-color: rgba(26, 161, 25, 0.15);
    padding: 2px 6px;
    border-radius: 4px;
    font-weight: 600;
  }

  .status-inactive {
    color: #ad4848;
    background-color: rgba(234, 108, 108, 0.15);
    padding: 2px 6px;
    border-radius: 4px;
    font-weight: 600;
  }

  .contract-table td small.text-muted {
    font-size: 0.75rem;
    display: block;
    margin-top: 2px;
  }

  /* === CSS UNTUK PRODUCT TABLE (meniru Deals/Quotation) === */
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

  /* === CSS UNTUK TOGGLE SWITCH === */
  .field-contract-status_contract {
    margin-bottom: 1rem;
  }

  .toggle-switch-container {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .toggle-switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 34px;
  }

  .toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
  }

  .toggle-switch .slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 34px;
  }

  .toggle-switch .slider:before {
    position: absolute;
    content: "";
    height: 26px;
    width: 26px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
  }

  .toggle-switch input:checked+.slider {
    background-color: #28a745;
  }

  /* Warna hijau saat Active */
  .toggle-switch input:disabled+.slider {
    background-color: #e9ecef;
    cursor: not-allowed;
  }

  .toggle-switch input:checked+.slider:before {
    transform: translateX(26px);
  }

  .toggle-switch-label {
    font-weight: 600;
    font-size: 1rem;
  }

  /* === SWEETALERT STYLING === */
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

  /* Style untuk tombol-tombol di footer form (meniru Quotation) */
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
yii\bootstrap5\Modal::end(); ?>

<?php Pjax::begin(['id' => 'gridDatacontract', 'timeout' => false]); ?>
<div class="contract-container">
  <div class="contract-header">
    <h2 class="contract-title"><?= Html::encode($this->title) ?></h2>
    <div class="search-add-bar">
      <div class="search-input-group">
        <?php $form = ActiveForm::begin([
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

        <?php \yii\widgets\ActiveForm::end(); ?>
      </div>
      <?= Html::button('<i class="bi bi-plus"></i> Add', ['class' => 'add-button', 'id' => 'add-contract-button']) ?>
    </div>
  </div>

  <div class="contract-table-wrapper">
    <table class="contract-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Code</th>
          <th>Customer</th>
          <th>Product</th>
          <th>Start Date</th>
          <th>End Date</th>
          <th>Salesperson</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($dataProvider->getCount() > 0): ?>
          <?php foreach ($dataProvider->getModels() as $index => $model): ?>
            <tr data-contract-id="<?= $model->contract_id ?>">
              <td><?= $index + 1 ?></td>
              <td><?= Html::encode($model->contract_code) ?></td>
              <td><?= Html::encode($model->customer->customer_name ?? '-') ?></td>
              <td><?= Html::encode($model->product->product_name ?? '-') ?></td>
              <td><?= !empty($model->start_date) ? Yii::$app->formatter->asDate($model->start_date, 'php:d-m-Y') : '-' ?></td>
              <td><?= !empty($model->end_date) ? Yii::$app->formatter->asDate($model->end_date, 'php:d-m-Y') : '-' ?></td>
              <td><?= Html::encode($model->deals->createdBy->username ?? '(not set)') ?></td>
              <td>
                <?php
                $statusClass = 'hover-text';
                $statusValue = strtolower($model->status_contract);

                $countdownText = '';
                if ($statusValue === 'active' && $model->end_date) {
                  $endDate = new \DateTime($model->end_date);
                  $today = new \DateTime();
                  $interval = $today->diff($endDate);
                  $isFuture = $today <= $endDate;

                  $countdownText = $isFuture
                    ? "{$interval->days} days left"
                    : "past {$interval->days} days";
                }

                if ($statusValue === 'active') {
                  $statusClass .= ' status-active';
                } elseif ($statusValue === 'inactive') {
                  $statusClass .= ' status-inactive';
                }

                echo Html::tag('span', Html::encode($model->status_contract), ['class' => $statusClass]);

                if ($statusValue === 'active' && $countdownText) {
                  echo "<br><small class='text-muted'>($countdownText)</small>";
                }
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
</div>
<div class="mt-3 d-flex justify-content-center">
  <?= LinkPager::widget(['pagination' => $dataProvider->pagination]); ?>
</div>
<?php Pjax::end(); ?>

<?php
JSRegister::begin(['position' => View::POS_END]);
?>
<script>
  $(document).ready(function() {
    function setModalTitle(title) {
      $('#modalHeader').html('<h5 class="modal-title">' + title + '</h5>');
    }

    $('#add-contract-button').click(function() {
      $('#modal').modal('show');
      setModalTitle('Add Legal Contract');
      $('#modalContent').html('<div class="text-center my-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
      $('#modalContent').load("<?= Url::to(['/sales/contract/create']) ?>", function(response, status, xhr) {
        if (status == "error") {
          $('#modalContent').html('<div class="alert alert-danger">Gagal memuat form. Cek koneksi atau periksa controller \"create\".</div>');
        }
      });
    });

    // Klik baris untuk buka detail contract
    $(document).on('click', '.contract-table tbody tr', function() {
      const contractId = $(this).data('contract-id');
      if (!contractId) return;

      $('#modal').modal('show');
      $('#modalHeader').html('<h5 class="modal-title">Detail Legal Contract</h5>');
      $('#modalContent').html('<div class="text-center my-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');

      $('#modalContent').load("/sales/contract/view?contract_id=" + contractId, function(response, status) {
        if (status === "error") {
          $('#modalContent').html('<div class="alert alert-danger">Gagal memuat data.</div>');
        }
      });
    });
  });
</script>
<?php JSRegister::end(); ?>