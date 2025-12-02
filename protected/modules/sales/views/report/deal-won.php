<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\ActiveForm;

$this->title = 'Report: Deals Won';
$this->params['breadcrumbs'][] = ['label' => 'Reports', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// --- Prepare Data for Chart ---
$chartLabelsJson = Json::encode($chartLabels);
$chartValuesJson = Json::encode($chartValues);

$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js', ['position' => \yii\web\View::POS_HEAD]);
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css');
?>

<style>
    body {
        background-color: #f5f8fa !important;
    }

    .report-page-container,
    .chart-card {
        background-color: #ffffff;
        padding: 25px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
    }

    .chart-card {
        margin-bottom: 25px;
    }

    .chart-card h5 {
        font-weight: 600;
        color: #3f4254;
    }

    .filter-container {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
        margin-bottom: 25px;
    }

    .filter-group {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: center;
    }

    .input-date {
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        padding: 6px 12px;
        font-size: 0.9rem;
    }

    .filter-btn {
        background-color: #27465E;
        color: white;
        border: none;
        border-radius: 0.375rem;
        padding: 8px 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        font-size: 0.9rem;
        gap: 6px;
    }

    .filter-btn:hover {
        background-color: #1a2e3c;
        color: white;
    }

    /* Style for table header to match */
    .kv-grid-table thead tr th {
        background-color: #e5e7eb !important;
        color: #3f4254 !important;
    }

    /* Menambahkan aturan ini untuk memperbaiki warna font pada header yang bisa di-sortir */
    .kv-grid-table thead tr th a {
        color: #3f4254 !important;
    }
</style>

<div class="report-page-container">
    <h1><?= Html::encode($this->title) ?></h1>
    <p class="text-muted">This report shows all successful sales transactions within the selected date range.</p>
    <hr class="mb-4">

    <div class="filter-container">
        <?php $form = ActiveForm::begin(['method' => 'get', 'action' => ['deal-won']]); ?>
        <div class="filter-group">
            <?= $form->field($filterModel, 'startDate', ['options' => ['tag' => false]])->input('date', ['class' => 'input-date'])->label('Start Date') ?>
            <span class="text-muted">-</span>
            <?= $form->field($filterModel, 'endDate', ['options' => ['tag' => false]])->input('date', ['class' => 'input-date'])->label('End Date') ?>

            <?= Html::submitButton('<i class="fa-solid fa-filter"></i> Apply', ['class' => 'filter-btn']) ?>
            <?= Html::a('<i class="fa-solid fa-rotate-right"></i> Reset', ['deal-won'], ['class' => 'filter-btn', 'title' => 'Reset']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>

    <div class="chart-card">
        <h5>Top 5 Products by Sales Value (Deal Won)</h5>
        <div style="height: 250px;">
            <canvas id="dealWonChart"></canvas>
        </div>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            [
                'attribute' => 'purchase_date',
                'format' => 'date',
            ],
            'deals_code',
            'customer.customer_name:text:Customer',
            'product.product_name:text:Product',
            'createdBy.username:text:Salesperson',
            [
                'attribute' => 'total',
                'label' => 'Transaction Value (Rp)',
                'format' => ['decimal', 0],
                'hAlign' => 'right',
            ],
        ],
        // --- Logic and Style Copied from deals-by-stage ---
        'bordered' => true,
        'striped' => true,
        'hover' => true,
        'panel' => [
            // Place the export button using the 'before' panel property
            'before' => '<div class="d-flex justify-content-end mb-3">{export}</div>',
            'heading' => false,
            'footer' => false,
            'after' => false,
        ],
        // Disable the default toolbar
        'toolbar' => false,
        'summary' => '',
        // Configure the export button
        'export' => [
            'label' => '</i> Export', // Use an icon
            'options' => [
                'class' => 'btn filter-btn', // Apply the same style as other buttons
                'title' => 'Export Report'
            ],
        ],
        // Configure the filenames for exported files
        'exportConfig' => [
            GridView::CSV => ['filename' => 'Report Deals Won - ' . date('Y-m-d')],
            GridView::EXCEL => ['filename' => 'Report Deals Won - ' . date('Y-m-d')],
            GridView::PDF => ['filename' => 'Report Deals Won - ' . date('Y-m-d')],
        ],
    ]); ?>
</div>

<?php
// JS for chart (No changes needed)
$js = <<<JS
const ctx = document.getElementById('dealWonChart');
if (ctx) {
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: $chartLabelsJson,
            datasets: [{
                label: 'Total Value',
                data: $chartValuesJson,
                backgroundColor: [
                    '#27465E', '#417FA9', '#FF1C71', '#FFA500', '#4CAF50'
                ],
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                }
            }
        },
    });
}
JS;
$this->registerJs($js);
?>