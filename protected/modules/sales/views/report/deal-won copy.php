<?php

use kartik\grid\GridView;
use yii\helpers\Html;

$this->title = 'Report: Deal Won';
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
    <p class="text-muted">Laporan ini menampilkan semua transaksi penjualan yang berhasil dimenangkan dalam rentang waktu yang dipilih.</p>
    <hr class="mb-4">

    <div class="row">
        <!-- Kolom Kiri untuk Tabel Data -->
        <div class="col-md-8">
            <?php
            echo GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    ['class' => 'kartik\grid\SerialColumn'],
                    [
                        'attribute' => 'purchase_date',
                        'format' => 'date',
                    ],
                    'deals_code',
                    [
                        'attribute' => 'customer.customer_name',
                        'label' => 'Customer',
                    ],
                    [
                        'attribute' => 'product.product_name',
                        'label' => 'Product',
                    ],
                    [
                        'attribute' => 'total',
                        'label' => 'Nilai Transaksi',
                        'format' => ['decimal', 0],
                        'hAlign' => 'right',
                    ],
                ],
                'panel' => [
                    'type' => GridView::TYPE_INFO, // Ganti warna panel
                    'heading' => '<i class="bi bi-trophy-fill"></i> Transaksi Berhasil',
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
                <p class="text-muted small">Filter berdasarkan Tanggal Pembelian.</p>
                <hr>
                <?= $this->render('_filterForm', ['model' => $filterModel]) ?>
            </div>
        </div>
    </div>
</div>