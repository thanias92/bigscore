<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Json;

$this->title = 'Report: Deals by Sales Representative';
$this->params['breadcrumbs'][] = ['label' => 'Reports', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Siapkan data untuk Chart.js
$chartLabelsJson = Json::encode($chartLabels);
$chartValuesJson = Json::encode($chartValues);

$this->registerCss("
    body { background-color: #f5f8fa !important; }
    .report-page-container {
        background-color: #ffffff;
        padding: 25px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
    }
    .filter-box {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
        height: 100%;
    }
    /* Style untuk card chart, meniru dashboard */
    .chart-card {
        background-color: #ffffff;
        padding: 25px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        margin-bottom: 25px;
        border: 1px solid #eff2f5;
    }
    .chart-card h5 {
        font-weight: 600;
        color: #3f4254;
    }
");

// Daftarkan library Chart.js
$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js', ['position' => \yii\web\View::POS_HEAD]);
?>

<div class="report-page-container">
    <h1><?= Html::encode($this->title) ?></h1>
    <p class="text-muted">Laporan ini menampilkan daftar semua deal yang ditangani oleh Sales Representative.</p>
    <hr class="mb-4">

    <!-- === BAGIAN CHART BARU === -->
    <div class="row">
        <div class="col-md-12">
            <div class="chart-card">
                <h5>Total Deals Handled per Representative</h5>
                <div style="height: 250px;">
                    <canvas id="dealsByRepChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Bagian Tabel dan Filter -->
    <div class="row">
        <div class="col-md-8">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    ['class' => 'kartik\grid\SerialColumn'],
                    [
                        'attribute' => 'createdBy.username',
                        'label' => 'Salesperson',
                    ],
                    'deals_code',
                    [
                        'attribute' => 'customer.customer_name',
                        'label' => 'Customer',
                    ],
                    [
                        'attribute' => 'product.product_name',
                        'label' => 'Produk',
                    ],
                    [
                        'attribute' => 'created_at', // Menggunakan created_at untuk tanggal
                        'format' => 'date',
                        'label' => 'Date Created'
                    ],
                    [
                        'attribute' => 'total',
                        'format' => ['decimal', 0],
                        'hAlign' => 'right',
                        'label' => 'Nilai (Rp)'
                    ],
                    'label_deals:text:Status',
                ],
                'panel' => [
                    'type' => GridView::TYPE_PRIMARY,
                    'heading' => '<i class="bi bi-list-task"></i> Daftar Deals',
                ],
                'bordered' => true,
                'striped' => true,
                'hover' => true,
            ]); ?>
        </div>

        <div class="col-md-4">
            <div class="filter-box">
                <h4><i class="bi bi-funnel-fill"></i> Filter</h4>
                <p class="text-muted small">Filter berdasarkan Tanggal Dibuat.</p>
                <hr>
                <?= $this->render('_filterForm', ['model' => $filterModel]) ?>
            </div>
        </div>
    </div>
</div>

<?php
// --- JavaScript untuk Grafik ---
$js = <<<JS
const ctx = document.getElementById('dealsByRepChart');
if (ctx) {
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: $chartLabelsJson,
            datasets: [{
                label: 'Total Deals',
                data: $chartValuesJson,
                backgroundColor: 'rgba(72, 129, 173, 0.6)',
                borderColor: 'rgba(72, 129, 173, 1)',
                borderWidth: 1,
                borderRadius: 5,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0 // Pastikan sumbu Y adalah bilangan bulat
                    }
                }
            },
            plugins: {
                legend: {
                    display: false // Sembunyikan legenda karena sudah jelas dari judul
                }
            }
        }
    });
}
JS;
$this->registerJs($js);
?>