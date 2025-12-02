<?php

use kartik\grid\GridView;
use yii\helpers\Html;

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
$chartLabelsJson = json_encode($chartLabels);
$chartDataJson = json_encode($chartData);
?>

<style>
    body {
        background-color: rgba(245, 248, 250, 1) !important;
    }

    .report-page-container {
        background-color: #ffffff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        flex-direction: column;
        overflow-y: auto;
    }

    .filter-box {
        background-color: #f8f9fa;
        /* Warna sedikit abu-abu */
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
        height: 100%;
    }

    .report-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .report-title {
        color: rgba(94, 98, 120, 1);
        font-family: 'Inter Semi Bold', sans-serif;
        /* Font Inter Semi Bold */
        font-weight: 600;
        font-size: 1.5rem;
    }

    /* --- Gaya Tabel Baru --- */
    /* Mengoverride gaya default GridView untuk menyamai "quotation-table" */
    .grid-view .panel {
        border: none;
        /* Hapus border panel default GridView */
        box-shadow: none;
        /* Hapus shadow panel default GridView */
    }

    .grid-view .table {
        width: 100%;
        border-collapse: collapse;
        border-spacing: 0;
        border: 1px solid rgba(192, 191, 192, 1);
        /* Stroke keseluruhan tabel */
        border-radius: 5px;
        /* Tambahkan border-radius ke tabel */
    }

    .grid-view .table thead th {
        background-color: rgba(229, 231, 235, 1);
        /* Warna header tabel */
        color: white !important;
        /* UBAH INI: Warna teks header tabel menjadi putih */
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid rgba(192, 191, 192, 1);
        font-family: 'Inter Semi Bold', sans-serif;
        font-weight: 600;
        position: sticky;
        /* Freeze header */
        top: 0;
        z-index: 1;
        /* Ensure header stays on top of content */
    }

    /* Rounded corners for thead cells */
    .grid-view .table thead tr:first-child th:first-child {
        border-top-left-radius: 5px;
    }

    .grid-view .table thead tr:first-child th:last-child {
        border-top-right-radius: 5px;
    }

    .grid-view .table tbody td {
        background-color: white;
        color: rgba(25, 25, 25, 1);
        padding: 10px;
        border-bottom: 1px solid rgba(192, 191, 192, 1);
        font-family: 'Inter Regular', sans-serif;
        cursor: pointer;
        /* Tambahkan cursor pointer untuk indikasi bisa diklik */
    }

    /* EFEK HOVER SEPERTI HALAMAN TICKETING */
    .grid-view .table tbody tr:hover td {
        background-color: #f5f5f5;
        /* Warna hover abu-abu muda */
    }

    .grid-view .table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Menyesuaikan toolbar GridView */
    .grid-view .panel-heading,
    .grid-view .panel-footer {
        display: none;
        /* Sembunyikan heading dan footer panel default */
    }

    .grid-view .summary {
        padding: 10px 0;
        font-family: 'Inter Regular', sans-serif;
        color: rgba(63, 66, 84, 1);
    }

    .grid-view .pagination>li>a,
    .grid-view .pagination>li>span {
        border-radius: 5px !important;
        margin: 0 2px;
        border: 1px solid rgba(192, 191, 192, 1);
        color: rgba(63, 66, 84, 1);
    }

    .grid-view .pagination>.active>a,
    .grid-view .pagination>.active>span {
        background-color: rgba(39, 70, 94, 1);
        border-color: rgba(39, 70, 94, 1);
        color: white;
    }

    .grid-view .btn-group {
        margin-bottom: 10px;
        /* Jarak antara toolbar dan tabel */
    }

    .grid-view .btn-group .btn {
        background-color: rgba(39, 70, 94, 1);
        border-color: rgba(39, 70, 94, 1);
        color: white;
    }

    .grid-view .btn-group .btn:hover {
        background-color: rgba(39, 70, 94, 0.8);
        border-color: rgba(39, 70, 94, 0.8);
    }

    /* Khusus untuk ekspor dropdown */
    .grid-view .dropdown-menu>li>a {
        color: rgba(25, 25, 25, 1);
    }
</style>

<div class="report-page-container">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-12">
            <div class="mb-4 p-3 border rounded" style="height: 400px; position: relative;">
                <canvas id="dealsByStageChart"></canvas>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <?php
            echo GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    ['class' => 'kartik\grid\SerialColumn'],
                    'label_deals:text:Deal Stage',
                    'deal_count:integer:Jumlah Deals',
                    [
                        'attribute' => 'opportunity_total',
                        'label' => 'Total Opportunity',
                        'format' => ['decimal', 0],
                        'hAlign' => 'right',
                        'width' => '15%', // Mengurangi lebar kolom
                    ],
                ],
                'panel' => [
                    'type' => GridView::TYPE_DEFAULT, // Ubah ke default agar tidak ada warna panel default
                    'heading' => false, // Sembunyikan heading default
                    'footer' => false, // Sembunyikan footer default
                ],
                'toolbar' => ['{export}', '{toggleData}'],
                'export' => ['fontAwesome' => true, 'label' => 'Ekspor'],
                'bordered' => false, // Matikan border bawaan GridView karena kita akan mengontrolnya dengan CSS
                'striped' => false, // Matikan striped bawaan GridView
                'hover' => false, // Matikan hover bawaan GridView karena kita akan mengontrolnya dengan CSS
                'responsive' => true,
                'summaryOptions' => ['class' => 'summary pull-left'], // Agar summary tidak di dalam panel footer
            ]);
            ?>
        </div>

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
// --- JavaScript untuk Grafik (Tidak ada perubahan) ---
$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js', ['position' => \yii\web\View::POS_HEAD]);
$js = <<<JS
const labels = $chartLabelsJson;
const data = {
    labels: labels,
    datasets: [{
        label: 'Jumlah Deals per Stage',
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
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } },
        plugins: {
            legend: { display: false },
            title: { display: true, text: 'Grafik Deals berdasarkan Stage' }
        }
    },
};
new Chart(document.getElementById('dealsByStageChart'), config);
JS;
$this->registerJs($js);
?>