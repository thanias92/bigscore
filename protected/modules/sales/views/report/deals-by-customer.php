<?php

use kartik\grid\GridView;
use yii\helpers\Html;

$this->title = 'Report: Deals by Customer';
$this->params['breadcrumbs'][] = ['label' => 'Reports', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

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
    <p class="text-muted">Laporan ini menampilkan ringkasan aktivitas penjualan untuk setiap customer.</p>
    <hr class="mb-4">

    <div class="row">
        <!-- Kolom Kiri untuk Tabel Data -->
        <div class="col-md-8">
            <?php
            echo GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    ['class' => 'kartik\grid\SerialColumn'],
                    'customer_name',
                    [
                        'attribute' => 'total_deals',
                        'label' => 'Total Deals',
                        'hAlign' => 'center',
                    ],
                    [
                        'attribute' => 'deals_won',
                        'label' => 'Won',
                        'hAlign' => 'center',
                    ],
                    [
                        'attribute' => 'deals_lost',
                        'label' => 'Lost',
                        'hAlign' => 'center',
                    ],
                    [
                        'attribute' => 'win_rate',
                        'label' => 'Win Rate',
                        'format' => ['percent', 2], // Format sebagai persentase
                        'hAlign' => 'center',
                    ],
                    [
                        'attribute' => 'total_revenue',
                        'label' => 'Total Revenue',
                        'format' => ['decimal', 0],
                        'hAlign' => 'right',
                    ],
                ],
                'panel' => [
                    'type' => GridView::TYPE_WARNING, // Ganti warna panel
                    'heading' => '<i class="bi bi-people-fill"></i> Ringkasan per Customer',
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
                <p class="text-muted small">Filter berdasarkan Tanggal Deal Dibuat.</p>
                <hr>
                <?= $this->render('_filterForm', ['model' => $filterModel]) ?>
            </div>
        </div>
    </div>
</div>