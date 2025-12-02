<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;

$this->title = 'Report: Customer by Sales';
$this->params['breadcrumbs'][] = ['label' => 'Reports', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// --- Siapkan data untuk Chart ---
$allCustomers = $dataProvider->getModels();
$customersBySales = ArrayHelper::index($allCustomers, null, function ($customer) {
    return $customer->createdBy->username ?? 'N/A';
});

$chartLabels = array_keys($customersBySales);
$chartValues = array_map('count', array_values($customersBySales));

$chartLabelsJson = Json::encode($chartLabels);
$chartValuesJson = Json::encode($chartValues);

$this->registerCss("
    body { background-color: #f5f8fa !important; }
    .report-page-container, .chart-card {
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
    .chart-card {
        margin-bottom: 25px;
    }
    .chart-card h5 {
        font-weight: 600;
        color: #3f4254;
    }
");

$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js', ['position' => \yii\web\View::POS_HEAD]);
?>

<div class="report-page-container">
    <h1><?= Html::encode($this->title) ?></h1>
    <p class="text-muted">Laporan ini menampilkan jumlah customer yang diakuisisi oleh setiap salesperson.</p>
    <hr class="mb-4">

    <div class="row">
        <!-- Kolom Kiri: Chart & Tabel -->
        <div class="col-md-8">
            <!-- Bagian Chart -->
            <div class="chart-card">
                <h5>Customers Acquired per Salesperson</h5>
                <div style="height: 250px;">
                    <canvas id="customerBySalesChart"></canvas>
                </div>
            </div>

            <!-- === PERUBAHAN UTAMA DI SINI: KONFIGURASI GRIDVIEW === -->
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    [
                        'class' => 'kartik\grid\SerialColumn',
                        'width' => '50px',
                    ],
                    [
                        'attribute' => 'createdBy.username',
                        'label' => 'Salesperson',
                        'group' => true,  // <-- KUNCI #1: Mengelompokkan baris berdasarkan salesperson
                        'subGroupOf' => 1, // <-- KUNCI #2: Membuat kolom lain menjadi sub-grup dari kolom ini
                        'groupOddCssClass' => 'kv-grouped-row',  // Style untuk baris ganjil
                        'groupEvenCssClass' => 'kv-grouped-row', // Style untuk baris genap
                    ],
                    [
                        'attribute' => 'customer_code',
                        'width' => '120px',
                    ],
                    'customer_name',
                    [
                        'attribute' => 'created_at',
                        'format' => 'date',
                        'width' => '150px',
                    ],
                ],
                'panel' => [
                    'type' => GridView::TYPE_PRIMARY,
                    'heading' => '<i class="bi bi-people-fill"></i> Daftar Customer oleh Salesperson',
                ],
                'bordered' => true,
                'striped' => true,
                'hover' => true,
                'toggleDataOptions' => ['minCount' => 20], // Tombol "show all" jika data > 20
            ]); ?>
        </div>

        <!-- Kolom Kanan: Filter -->
        <div class="col-md-4">
            <div class="filter-box">
                <h4><i class="bi bi-funnel-fill"></i> Filter</h4>
                <p class="text-muted small">Filter berdasarkan Tanggal Customer Dibuat.</p>
                <hr>
                <?= $this->render('_filterForm', ['model' => $filterModel]) ?>
            </div>
        </div>
    </div>
</div>

<?php
// --- JavaScript untuk Grafik ---
$js = <<<JS
const ctx = document.getElementById('customerBySalesChart');
if (ctx) {
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: $chartLabelsJson,
            datasets: [{
                label: 'Jumlah Customer',
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
                    display: false
                }
            }
        }
    });
}
JS;
$this->registerJs($js);
?>