<?php

use kartik\grid\GridView;
use yii\helpers\Html;

$this->title = 'Report: Product Sales';
$this->params['breadcrumbs'][] = ['label' => 'Reports', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// --- Menyiapkan Data untuk Grafik ---
$chartLabels = [];
$chartData = [];
foreach ($dataProvider->getModels() as $model) {
    $chartLabels[] = $model['product_name'];
    $chartData[] = (float)$model['total_revenue']; // Ambil data pendapatan
}
$chartLabelsJson = json_encode($chartLabels);
$chartDataJson = json_encode($chartData);

// --- Menambahkan Style Halaman ---
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
");
?>

<div class="report-page-container">
    <h1><?= Html::encode($this->title) ?></h1>

    <!-- Baris untuk Grafik -->
    <div class="row">
        <div class="col-12">
            <div class="mb-4 p-3 border rounded" style="height: 400px; position: relative;">
                <canvas id="productSalesChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Baris Baru untuk Tabel dan Filter -->
    <div class="row">
        <!-- Kolom Kiri untuk Tabel Data -->
        <div class="col-md-8">
            <?php
            echo GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    ['class' => 'kartik\grid\SerialColumn'],
                    'product_name',
                    [
                        'attribute' => 'total_units_sold',
                        'label' => 'Total Unit Terjual',
                        'hAlign' => 'center',
                    ],
                    [
                        'attribute' => 'total_revenue',
                        'label' => 'Total Pendapatan',
                        'format' => ['decimal', 0],
                        'hAlign' => 'right',
                    ],
                ],
                'panel' => [
                    'type' => GridView::TYPE_SUCCESS, // Ganti warna panel
                    'heading' => '<i class="bi bi-bar-chart-line-fill"></i> Detail Laporan',
                ],
                'toolbar' => ['{export}', '{toggleData}'],
                'export' => ['fontAwesome' => true, 'label' => 'Ekspor'],
                'bordered' => true,
                'striped' => true,
                'hover' => true,
            ]);
            ?>
        </div>

        <!-- Kolom Kanan untuk Form Filter -->
        <div class="col-md-4">
            <div class="filter-box">
                <h4><i class="bi bi-funnel-fill"></i> Filter</h4>
                <hr>
                <?= $this->render('_filterForm', ['model' => $filterModel]) ?>
            </div>
        </div>
    </div>
</div>

<?php
// --- JavaScript untuk Grafik ---
$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js', ['position' => \yii\web\View::POS_HEAD]);
$js = <<<JS
const labels = $chartLabelsJson;
const data = {
    labels: labels,
    datasets: [{
        label: 'Total Pendapatan',
        backgroundColor: [ // Sediakan beberapa warna untuk pie chart
            'rgba(255, 99, 132, 0.6)',
            'rgba(54, 162, 235, 0.6)',
            'rgba(255, 206, 86, 0.6)',
            'rgba(75, 192, 192, 0.6)',
            'rgba(153, 102, 255, 0.6)',
            'rgba(255, 159, 64, 0.6)'
        ],
        borderColor: '#fff',
        borderWidth: 1,
        data: $chartDataJson,
    }]
};
const config = {
    type: 'pie', // Ganti tipe chart menjadi 'pie'
    data: data,
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
            },
            title: {
                display: true,
                text: 'Grafik Pendapatan per Produk'
            }
        }
    },
};
new Chart(document.getElementById('productSalesChart'), config);
JS;
$this->registerJs($js);
?>