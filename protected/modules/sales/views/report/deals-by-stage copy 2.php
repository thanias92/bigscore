<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\ActiveForm;

$this->title = 'Report: Deals by Stage';
$this->params['breadcrumbs'][] = ['label' => 'Reports', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// --- Menyiapkan Data untuk Grafik ---
$chartLabels = [];
$chartData = [];
foreach ($dataProvider->getModels() as $model) {
    $chartLabels[] = $model['label_deals'];
    $chartData[] = (int)$model['deal_count'];
}
$chartLabelsJson = Json::encode($chartLabels);
$chartDataJson = Json::encode($chartData);
$chartColorsJson = Json::encode($chartColors);

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

    /* Style untuk Filter Baru */
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
        padding: 6px 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }

    .filter-btn:hover {
        background-color: #1a2e3c;
        color: white;
    }
</style>

<div class="report-page-container">
    <h1><?= Html::encode($this->title) ?></h1>
    <p class="text-muted">Laporan ini menampilkan ringkasan jumlah dan nilai deals berdasarkan setiap tahapan pipeline.</p>
    <hr class="mb-4">

    <!-- Filter Section -->
    <?php $form = ActiveForm::begin(['method' => 'get', 'action' => ['deals-by-stage']]); ?>
    <div class="filter-group">
        <?= $form->field($filterModel, 'startDate', ['options' => ['tag' => false]])->input('date', ['class' => 'input-date'])->label(false) ?>
        <span class="text-muted">-</span>
        <?= $form->field($filterModel, 'endDate', ['options' => ['tag' => false]])->input('date', ['class' => 'input-date'])->label(false) ?>

        <?= Html::submitButton('<i class="fa-solid fa-filter"></i> Apply', ['class' => 'filter-btn']) ?>
        <?= Html::a('<i class="fa-solid fa-rotate-right"></i>', ['deals-by-stage'], ['class' => 'filter-btn', 'title' => 'Reset']) ?>
    </div>
    <?php ActiveForm::end(); ?>

    <!-- Chart Section -->
    <div class="chart-card">
        <h5>Grafik Deals berdasarkan Stage</h5>
        <div style="height: 250px;">
            <canvas id="dealsByStageChart"></canvas>
        </div>
    </div>

    <!-- GridView Section -->
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            'label_deals:text:Deal Stage',
            'deal_count:integer:Jumlah Deals',
            [
                'attribute' => 'opportunity_total',
                'label' => 'Total Opportunity (Rp)',
                'format' => ['decimal', 0],
                'hAlign' => 'right',
            ],
        ],
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
            'heading' => '<i class="bi bi-bar-chart-steps"></i> Ringkasan Deals',
        ],
        'bordered' => true,
        'striped' => true,
        'hover' => true,
        'toolbar' => [
            '{export}', // Hanya tampilkan tombol export
        ],
        'export' => [
            'fontAwesome' => true,
        ],
    ]); ?>
</div>

<?php
$js = <<<JS
const ctx = document.getElementById('dealsByStageChart');
if (ctx) {
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: $chartLabelsJson,
            datasets: [{
                label: 'Jumlah Deals',
                data: $chartDataJson,
                backgroundColor: $chartColorsJson, // Gunakan warna dari controller
                borderRadius: 5,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
            plugins: {
                legend: { display: false },
            }
        },
    });
}
JS;
$this->registerJs($js);
?>