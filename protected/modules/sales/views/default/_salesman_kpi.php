<?php

use yii\helpers\Html;
use yii\helpers\Json;

// Data Visit
$actualVisit = $salesman['visitData']['actual'];
$targetVisit = $salesman['visitData']['target'];
$sisaTarget = ($targetVisit > $actualVisit) ? $targetVisit - $actualVisit : 0;
$visitValues = [$actualVisit, $sisaTarget];

// Data Pipeline
$pipelineLabels = array_column($salesman['pipelineData'], 'label_deals');
$pipelineValues = array_column($salesman['pipelineData'], 'deal_count');

// Encode ke JSON
$visitValuesJson = Json::encode($visitValues);
$pipelineLabelsJson = Json::encode($pipelineLabels);
$pipelineValuesJson = Json::encode($pipelineValues);
$salesmanId = $salesman['id'];
?>

<div class="row mb-4">
    <!-- Grafik Visit Target -->
    <div class="col-md-5 mb-4 mb-md-0">
        <div class="chart-card h-100">
            <h6>Visit Target by <?= Html::encode($salesman['username']) ?></h6>
            <div class="d-flex align-items-center justify-content-center mt-3">
                <div style="position: relative; width: 180px; height: 180px;">
                    <canvas id="visitChart-<?= $salesmanId ?>"></canvas>
                    <div class="chart-center-text">
                        <div class="chart-value fw-bold" style="font-size: 1.5rem;"><?= $actualVisit ?></div>
                        <div class="chart-value">of <?= $targetVisit ?> Visits</div>
                    </div>
                </div>
                <div id="visitLegend-<?= $salesmanId ?>" class="ms-4"></div>
            </div>
        </div>
    </div>

    <!-- Grafik Sales Pipeline -->
    <div class="col-md-7">
        <div class="chart-card h-100">
            <h6>Sales Pipeline by <?= Html::encode($salesman['username']) ?></h6>
            <div class="mt-3" style="height: 200px;">
                <canvas id="pipelineChart-<?= $salesmanId ?>"></canvas>
            </div>
        </div>
    </div>
</div>

<?php
$js = <<<JS

// 1. Grafik Doughnut Visit Target
const visitCtx_{$salesmanId} = document.getElementById('visitChart-{$salesmanId}');
if (visitCtx_{$salesmanId}) {
    const visitChart = new Chart(visitCtx_{$salesmanId}, {
        type: 'doughnut',
        data: {
            labels: ['Actual Visit', 'Target Visit'],
            datasets: [{
                data: $visitValuesJson,
                backgroundColor: ['#27465E', '#FF1C71'], // Biru Tua, Pink
                borderWidth: 0,
                cutout: '80%',
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { 
                legend: { display: false }, 
                tooltip: { enabled: true },
                datalabels: {
                    formatter: (value, ctx) => {
                        const total = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                        if (total === 0) return '';
                        const percentage = (value / total * 100);
                        return percentage > 1 ? percentage.toFixed(0) + '%' : '';
                    },
                    color: '#fff',
                    font: { weight: 'bold', size: 14 }
                }
            }
        }
    });
    
    const visitLegendContainer = document.getElementById('visitLegend-{$salesmanId}');
    visitLegendContainer.innerHTML = visitChart.data.labels.map((label, i) => {
        const color = visitChart.data.datasets[0].backgroundColor[i];
        return '<div class="d-flex align-items-center mb-2">' +
                   '<div style="width:12px; height:12px; background:' + color + '; margin-right:8px; border-radius: 2px;"></div>' +
                   '<span>' + label + '</span>' +
               '</div>';
    }).join('');
}

// 2. Grafik Bar Sales Pipeline
const pipelineCtx_{$salesmanId} = document.getElementById('pipelineChart-{$salesmanId}');
if (pipelineCtx_{$salesmanId}) {
    new Chart(pipelineCtx_{$salesmanId}, {
        type: 'bar',
        data: {
            labels: $pipelineLabelsJson,
            datasets: [{
                data: $pipelineValuesJson,
                backgroundColor: [
                    'rgba(9, 148, 153, 0.3)', 'rgba(9, 148, 153, 0.5)',
                    'rgba(9, 148, 153, 0.8)', 'rgba(26, 161, 25, 1)',
                    'rgba(201, 40, 30, 0.8)'
                ],
                borderRadius: 5, barThickness: 25,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            scales: { 
                x: { grid: { display: false } },
                y: { grid: { display: false }, beginAtZero: true, ticks: { precision: 0 } } 
            },
            plugins: { 
                legend: { display: false }, tooltip: { enabled: true },
                datalabels: { display: false }
            }
        }
    });
}
JS;
$this->registerJs($js);
?>