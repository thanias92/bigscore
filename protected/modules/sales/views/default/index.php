<?php

use yii\helpers\Html;
use yii\helpers\Json;

$this->title = 'Sales Dashboard';
$this->params['breadcrumbs'][] = $this->title;

// Data Pipeline
$pipelineLabels = array_column($pipelineData, 'label_deals');
$pipelineValues = array_column($pipelineData, 'deal_count');
$pipelineLabelsJson = Json::encode($pipelineLabels);
$pipelineValuesJson = Json::encode($pipelineValues);

// Data Product
$productLabels = array_column($productSoldData, 'product_name');
$productValues = array_column($productSoldData, 'total_units_sold');
$totalProducts = array_sum($productValues);
$productLabelsJson = Json::encode($productLabels);
$productValuesJson = Json::encode($productValues);

// Data Visit
$kpiVisitLabels = array_column($kpiData['salesmenVisits'], 'username');
$kpiVisitValues = array_column($kpiData['salesmenVisits'], 'visit_count');
$kpiVisitLabelsJson = Json::encode($kpiVisitLabels);
$kpiVisitValuesJson = Json::encode($kpiVisitValues);
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

<style>
    body {
        background-color: #f5f8fa !important;
    }

    .card-stat,
    .chart-card {
        background-color: #ffffff;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        margin-bottom: 25px;
        border: 1px solid #eff2f5;
    }

    .card-stat .stat-number {
        font-size: 2rem;
        font-weight: 700;
        color: #181c32;
    }

    .card-stat .stat-label {
        font-size: 1rem;
        color: #a1a5b7;
        font-weight: 500;
    }

    .card-stat .stat-icon {
        font-size: 2.5rem;
        color: #e1e3ea;
    }

    .section-title {
        font-size: 1.3rem;
        font-weight: 600;
        color: #181c32;
        margin-bottom: 20px;
    }

    .chart-card h6 {
        font-weight: 600;
        color: #3f4254;
        margin-bottom: 15px;
    }

    /* Style untuk Doughnut Chart */
    .chart-container-doughnut {
        position: relative;
        width: 220px;
        height: 220px;
        margin: auto;
    }

    .chart-center-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        pointer-events: none;
    }

    .chart-percentage {
        font-size: 2.25rem;
        font-weight: 700;
        color: #181c32;
        line-height: 1;
    }

    .chart-value {
        font-size: 1rem;
        color: #a1a5b7;
    }
</style>

<div class="dashboard-container">

    <!-- === CONTAINER ATAS: RINGKASAN & GRAFIK UMUM === -->
    <div class="chart-card mt-4 p-4">
        <div class="row">
            <div class="col-md-3">
                <div class="card-stat d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-number"><?= Html::encode($summary['totalCustomer']) ?></div>
                        <div class="stat-label">Total Customer</div>
                    </div>
                    <!-- ICON BARU -->
                    <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M11.3746 6.37143C9.95795 6.37143 8.80952 7.51985 8.80952 8.93651C8.80952 10.3532 9.95795 11.5016 11.3746 11.5016C12.7913 11.5016 13.9397 10.3532 13.9397 8.93651C13.9397 7.51985 12.7913 6.37143 11.3746 6.37143ZM7.4381 8.93651C7.4381 6.76243 9.20053 5 11.3746 5C13.5487 5 15.3111 6.76243 15.3111 8.93651C15.3111 11.1106 13.5487 12.873 11.3746 12.873C9.20053 12.873 7.4381 11.1106 7.4381 8.93651ZM15.7927 5.8482C15.9947 5.52786 16.4181 5.43195 16.7385 5.63396C17.8303 6.32248 18.5619 7.53608 18.5619 8.93651C18.5619 10.3369 17.8303 11.5505 16.7385 12.2391C16.4181 12.4411 15.9947 12.3452 15.7927 12.0248C15.5907 11.7045 15.6866 11.281 16.0069 11.079C16.7193 10.6298 17.1905 9.84415 17.1905 8.93651C17.1905 8.02887 16.7193 7.24323 16.0069 6.79398C15.6866 6.59197 15.5907 6.16853 15.7927 5.8482ZM11.3746 15.3111C8.60817 15.3111 6.37143 17.5479 6.37143 20.3143C6.37143 20.693 6.06442 21 5.68571 21C5.307 21 5 20.693 5 20.3143C5 16.7904 7.85075 13.9397 11.3746 13.9397C14.8985 13.9397 17.7492 16.7904 17.7492 20.3143C17.7492 20.693 17.4422 21 17.0635 21C16.6848 21 16.3778 20.693 16.3778 20.3143C16.3778 17.5479 14.141 15.3111 11.3746 15.3111ZM17.6401 15.4502C17.8758 15.1538 18.3072 15.1045 18.6036 15.3402C20.07 16.506 21 18.3021 21 20.3143C21 20.693 20.693 21 20.3143 21C19.9356 21 19.6286 20.693 19.6286 20.3143C19.6286 18.7344 18.9006 17.3284 17.7502 16.4137C17.4537 16.178 17.4045 15.7467 17.6401 15.4502Z" fill="#4881AD" />
                    </svg>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-stat d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-number"><?= number_format($summary['totalSales']) ?></div>
                        <div class="stat-label">Total Sales (Won)</div>
                    </div>
                    <!-- ICON BARU -->
                    <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20.9748 13.6346C20.8533 15.1614 20.2961 16.6212 19.3694 17.8406C18.4426 19.0601 17.1854 19.9878 15.7468 20.5136C14.3083 21.0395 12.7491 21.1414 11.2544 20.8073C9.75969 20.473 8.39234 19.7168 7.31479 18.6283C6.23724 17.5399 5.49478 16.165 5.17562 14.6671C4.85646 13.1691 4.97402 11.611 5.51433 10.1778C6.05463 8.74469 6.99498 7.49678 8.22364 6.58234C9.45231 5.6679 10.9176 5.12538 12.4456 5.01923M16.4339 14.3415C16.2116 14.9136 15.8503 15.4212 15.3825 15.8185C14.9148 16.2158 14.3553 16.4902 13.7549 16.617C13.1544 16.7438 12.5318 16.7189 11.9433 16.5445C11.3549 16.3702 10.8192 16.052 10.3847 15.6186C9.95018 15.1851 9.63054 14.6503 9.4547 14.0623C9.27884 13.4743 9.25233 12.8518 9.37755 12.251C9.50278 11.6501 9.77579 11.09 10.1719 10.6213C10.568 10.1525 11.0747 9.78984 11.6462 9.56611M13 13L16.0769 9.92308M16.0769 9.92308L18.5385 10.5385L21 8.07692L18.5385 7.46154L17.9231 5L15.4615 7.46154L16.0769 9.92308Z" stroke="#4881AD" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <div class="chart-card" style="height: 400px;">
                    <h6>Sales Pipeline</h6>
                    <canvas id="salesPipelineChart"></canvas>
                </div>
            </div>
            <div class="col-md-4">
                <div class="chart-card h-100 d-flex flex-column">
                    <h6>Product Sold</h6>
                    <div class="flex-grow-1 d-flex align-items-center justify-content-center">
                        <div class="chart-container-doughnut" style="width: 180px; height: 180px;">
                            <canvas id="productSoldChart"></canvas>
                            <div class="chart-center-text">
                                <div class="chart-value">Total</div>
                                <div class="chart-percentage" style="font-size: 2rem;"><?= $totalProducts ?></div>
                                <div class="chart-value">Units</div>
                            </div>
                        </div>
                        <div id="productSoldLegend" class="ms-4"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- === CONTAINER BAWAH (KPI) === -->
    <div class="chart-card mt-4 p-4">
        <div class="section-title">KEY PERFOMANCE INDEX</div>

        <div class="row">
            <div class="col-md-3">
                <div class="card-stat d-flex justify-content-between align-items-center" style="margin-bottom: 0; box-shadow: 5px; border: 1px solid #eff2f5;">
                    <div>
                        <div class="stat-number"><?= Html::encode($kpiData['salesmenCount']) ?></div>
                        <div class="stat-label">Salesman</div>
                    </div>
                    <!-- ICON BARU UNTUK SALESMAN -->
                    <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M12.9996 6.37143C11.5829 6.37143 10.4345 7.51985 10.4345 8.93651C10.4345 10.3532 11.5829 11.5016 12.9996 11.5016C14.4163 11.5016 15.5647 10.3532 15.5647 8.93651C15.5647 7.51985 14.4163 6.37143 12.9996 6.37143ZM9.0631 8.93651C9.0631 6.76243 10.8255 5 12.9996 5C15.1737 5 16.9361 6.76243 16.9361 8.93651C16.9361 11.1106 15.1737 12.873 12.9996 12.873C10.8255 12.873 9.0631 11.1106 9.0631 8.93651ZM12.9996 15.3111C10.2332 15.3111 7.99643 17.5479 7.99643 20.3143C7.99643 20.693 7.68942 21 7.31071 21C6.932 21 6.625 20.693 6.625 20.3143C6.625 16.7904 9.47575 13.9397 12.9996 13.9397C16.5235 13.9397 19.3742 16.7904 19.3742 20.3143C19.3742 20.693 19.0672 21 18.6885 21C18.3098 21 18.0028 20.693 18.0028 20.3143C18.0028 17.5479 15.766 15.3111 12.9996 15.3111Z" fill="#4881AD" />
                    </svg>
                </div>
            </div>
            <div class="col-md-9">
                <div class="h-100" style="box-shadow: 5px;">
                    <h6>Customer Visit this month</h6>
                    <div style="height: 150px;">
                        <canvas id="customerVisitChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <hr class="my-4">

        <!-- Performa Salesman Individu (Looping) -->
        <?php foreach ($salesmanPerformance as $salesman): ?>
            <?= $this->render('_salesman_kpi', ['salesman' => $salesman]) ?>
        <?php endforeach; ?>
    </div>

</div>

<?php
// --- JavaScript untuk Grafik Utama ---
$js = <<<JS

Chart.register(ChartDataLabels);

// 1. Grafik Overall Sales Pipeline
const pipelineCtx = document.getElementById('salesPipelineChart');
if (pipelineCtx) {
    new Chart(pipelineCtx, {
        type: 'bar',
        data: {
            labels: $pipelineLabelsJson,
            datasets: [{
                data: $pipelineValuesJson,
                backgroundColor: [
                    'rgba(9, 148, 153, 0.3)',   // New
                    'rgba(9, 148, 153, 0.5)',   // Proposal Sent
                    'rgba(9, 148, 153, 0.8)',   // Negotiation
                    'rgba(26, 161, 25, 1)',     // Deal Won
                    'rgba(201, 40, 30, 0.8)'    // Deal Lost
                ],
                borderRadius: 5,
                barThickness: 40,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
            plugins: { 
                legend: { display: false },
                datalabels: {
                    anchor: 'end',
                    align: 'top',
                    color: '#555',
                    font: { weight: 'bold' }
                }
            }
        }
    });
}

// 2. Grafik Overall Product Sold
const productCtx = document.getElementById('productSoldChart');
if (productCtx) {
    const productChart = new Chart(productCtx, {
        type: 'doughnut',
        data: {
            labels: $productLabelsJson,
            datasets: [{
                data: $productValuesJson,
                backgroundColor: ['#27465E', '#417FA9', '#FF1C71', '#FFA500', '#4CAF50'],
                borderWidth: 0,
                cutout: '80%'
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { 
                legend: { display: false },
                datalabels: {
                    formatter: (value, ctx) => {
                        const total = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                        const percentage = (value / total * 100);
                        return percentage > 1 ? percentage.toFixed(0) + '%' : '';
                    },
                    color: '#fff',
                    font: { weight: 'bold' }
                }
            }
        }
    });

    // Buat legenda kustom
    const productLegendContainer = document.getElementById('productSoldLegend');
    productLegendContainer.innerHTML = productChart.data.labels.map((label, i) => {
        const color = productChart.data.datasets[0].backgroundColor[i];
        return '<div class="d-flex align-items-center mb-2">' +
                   '<div style="width:12px; height:12px; background:' + color + '; margin-right:8px; border-radius: 2px;"></div>' +
                   '<span>' + label + '</span>' +
               '</div>';
    }).join('');
}

// 3. Grafik KPI Customer Visit
const visitCtx = document.getElementById('customerVisitChart');
if (visitCtx) {
    new Chart(visitCtx, {
        type: 'bar',
        data: {
            labels: $kpiVisitLabelsJson,
            datasets: [{
                data: $kpiVisitValuesJson,
                backgroundColor: '#417FA9',
                borderRadius: 5,
                barThickness: 30,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
            plugins: { 
                legend: { display: false },
                datalabels: {
                    anchor: 'end',
                    align: 'top',
                    color: '#555',
                    font: { weight: 'bold' }
                }
            }
        }
    });
}

JS;
$this->registerJs($js, \yii\web\View::POS_END);
?>