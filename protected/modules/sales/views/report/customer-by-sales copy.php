<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use kartik\grid\GridView;

$this->title = 'Report: Customer by Sales';
$this->params['breadcrumbs'][] = ['label' => 'Reports', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// --- 1. MENYIAPKAN DATA UNTUK GRAFIK DAN ACCORDION ---
$allDeals = $dataProvider->getModels();
$salesDataGrouped = ArrayHelper::index($allDeals, null, 'salesperson_name');
ksort($salesDataGrouped);

$chartLabels = [];
$chartData = [];
foreach ($salesDataGrouped as $salespersonName => $deals) {
    $chartLabels[] = $salespersonName;
    $chartData[] = array_sum(array_column($deals, 'total'));
}
$chartLabelsJson = json_encode($chartLabels);
$chartDataJson = json_encode($chartData);


// --- 2. MENAMBAHKAN STYLE HALAMAN ---
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
    .accordion-button:not(.collapsed) { color: #0c63e4; background-color: #e7f1ff; }
    .accordion-button strong { flex-shrink: 0; margin-right: 15px; }
    .table-sm th, .table-sm td { padding: 0.4rem; }
    .report-table thead th {
        background-color: rgba(229, 231, 235, 1) !important;
        color: rgba(63, 66, 84, 1) !important;
        font-weight: 600;
    }
");
?>

<div class="report-page-container">
    <h1><?= Html::encode($this->title) ?></h1>
    <p class="text-muted">Laporan ini menampilkan kinerja penjualan, daftar customer, dan produk yang berhasil diakuisisi oleh setiap salesperson.</p>
    <hr class="mb-4">

    <div class="row">
        <div class="col-md-8">
            <div class="mb-4 p-3 border rounded">
                <canvas id="salesPerformanceChart" style="height: 350px;"></canvas>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="m-0">Detail Transaksi per Salesperson</h4>
                <div style="width: 100px;">
                    <?php
                    // ===============================================
                    // PERUBAHAN DI SINI: Menambahkan kembali 'columns'
                    // ===============================================
                    echo GridView::widget([
                        'dataProvider' => $dataProvider,
                        'columns' => [
                            ['class' => 'kartik\grid\SerialColumn'],
                            'salesperson_name',
                            [
                                'attribute' => 'purchase_date',
                                'format' => 'date',
                            ],
                            'customer_name',
                            'product_name',
                            [
                                'attribute' => 'total',
                                'format' => ['decimal', 0],
                                'hAlign' => 'right',
                                'label' => 'Nilai Deal (Rp)'
                            ],
                        ],
                        'panel' => false,
                        'toolbar' => ['{export}'],
                        'export' => ['fontAwesome' => true, 'label' => 'Ekspor'],
                        'layout' => '{toolbar}',
                    ]);
                    ?>
                </div>
            </div>

            <div class="accordion" id="accordionCustomerBySales">
                <?php if (empty($salesDataGrouped)) : ?>
                    <div class="alert alert-warning">Tidak ada data untuk ditampilkan.</div>
                    <?php else :
                    $i = 0;
                    foreach ($salesDataGrouped as $salespersonName => $deals) :
                        $totalSalesValue = array_sum(array_column($deals, 'total'));
                    ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading-<?= $i ?>">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?= $i ?>" aria-expanded="true" aria-controls="collapse-<?= $i ?>">
                                    <strong><?= Html::encode($salespersonName) ?></strong>
                                    <span class="ms-auto d-flex align-items-center gap-2">
                                        <span class="badge bg-success rounded-pill"><i class="bi bi-cash-coin"></i> Rp <?= number_format($totalSalesValue, 0, ',', '.') ?></span>
                                        <span class="badge bg-primary rounded-pill"><i class="bi bi-person-check-fill"></i> <?= count($deals) ?> Transaksi</span>
                                    </span>
                                </button>
                            </h2>
                            <div id="collapse-<?= $i ?>" class="accordion-collapse collapse show" aria-labelledby="heading-<?= $i ?>" data-bs-parent="#accordionCustomerBySales">
                                <div class="accordion-body p-2">
                                    <table class="table table-striped table-hover table-sm mb-0 report-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 15%;">Tanggal</th>
                                                <th style="width: 35%;">Customer</th>
                                                <th style="width: 30%;">Produk</th>
                                                <th style="width: 20%;" class="text-end">Nilai Deal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($deals as $deal) : ?>
                                                <tr>
                                                    <td><?= Yii::$app->formatter->asDate($deal['purchase_date'], 'medium') ?></td>
                                                    <td><?= Html::a(Html::encode($deal['customer_name']), ['/customer/view', 'id' => $deal['customer_id']], ['target' => '_blank', 'data-pjax' => '0']) ?></td>
                                                    <td><?= Html::encode($deal['product_name']) ?></td>
                                                    <td class="text-end">Rp <?= number_format($deal['total'], 0, ',', '.') ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                <?php $i++;
                    endforeach;
                endif; ?>
            </div>
        </div>

        <div class="col-md-4">
            <div class="filter-box mb-4">
                <h4><i class="bi bi-funnel-fill"></i> Filter</h4>
                <p class="text-muted small">Filter berdasarkan Tanggal Pembelian.</p>
                <hr>
                <?= $this->render('_filterForm', ['model' => $filterModel]) ?>
            </div>
        </div>
    </div>
</div>

<?php
// --- JAVASCRIPT UNTUK GRAFIK ---
$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js', ['position' => \yii\web\View::POS_HEAD]);
$js = <<<JS
const labels = $chartLabelsJson;
const data = {
    labels: labels,
    datasets: [{
        label: 'Total Penjualan (Rp)',
        backgroundColor: 'rgba(54, 162, 235, 0.6)',
        borderColor: 'rgba(54, 162, 235, 1)',
        borderWidth: 1,
        data: $chartDataJson,
    }]
};
const config = {
    type: 'bar',
    data: data,
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            title: {
                display: true,
                text: 'Grafik Kinerja Penjualan per Salesperson',
                font: { size: 16 }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value, index, values) {
                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                    }
                }
            }
        }
    },
};
new Chart(document.getElementById('salesPerformanceChart'), config);
JS;
$this->registerJs($js);
?>