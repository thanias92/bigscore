<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Sales Reports';
$this->params['breadcrumbs'][] = $this->title;
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.min.css">

<style>
    body {
        background-color: #f5f8fa !important;
        overflow: hidden;
    }

    .report-index-container {
        background-color: #ffffff;
        padding: 25px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        height: calc(100vh - 100px);
        overflow-y: auto;
    }

    .report-header {
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
        margin-bottom: 25px;
    }

    .report-title {
        color: #5e6278;
        font-weight: 600;
        font-size: 1.75rem;
    }

    .report-category h4 {
        color: #3f4254;
        font-weight: 600;
        margin-bottom: 15px;
        font-size: 1.1rem;
        border-left: 3px solid #0d6efd;
        /* Primary color accent */
        padding-left: 10px;
    }

    .list-group-item-action {
        color: #5e6278;
        font-weight: 500;
        border-radius: 6px !important;
        /* Rounded corners for items */
        margin-bottom: 8px;
        border-left: 3px solid transparent;
        transition: all 0.2s ease-in-out;
    }

    .list-group-item-action:hover,
    .list-group-item-action:focus {
        background-color: #f8f9fa;
        color: #0d6efd;
        transform: translateX(5px);
        border-left: 3px solid #0d6efd;
    }
</style>

<div class="report-index-container">
    <div class="report-header">
        <h1 class="report-title"><?= Html::encode($this->title) ?></h1>
    </div>

    <!-- Gunakan satu baris untuk menampung ketiga kolom -->
    <div class="row">
        <!-- Kolom 1: Deal Reports (1/3 lebar) -->
        <div class="col-md-4 report-category">
            <h4><i class="bi bi-graph-up-arrow"></i> Deal Reports</h4>
            <div class="list-group">
                <?= Html::a('Deals by Stage', ['/sales/report/deals-by-stage'], ['class' => 'list-group-item list-group-item-action']) ?>
                <?= Html::a('Deal Won Report', ['/sales/report/deal-won'], ['class' => 'list-group-item list-group-item-action']) ?>
                <?= Html::a('Deals by Sales Representative', ['/sales/report/deals-by-sales-representative'], ['class' => 'list-group-item list-group-item-action']) ?>
                <?= Html::a('Deals by Customer', ['/sales/report/deals-by-customer'], ['class' => 'list-group-item list-group-item-action']) ?>
            </div>
        </div>

        <!-- Kolom 2: Product Reports (1/3 lebar) -->
        <div class="col-md-4 report-category">
            <h4><i class="bi bi-box-seam"></i> Product Reports</h4>
            <div class="list-group">
                <?= Html::a('Product Sales Report', ['/sales/report/product-sales'], ['class' => 'list-group-item list-group-item-action']) ?>
            </div>
        </div>

        <!-- Kolom 3: Customer Reports (1/3 lebar) -->
        <div class="col-md-4 report-category">
            <h4><i class="bi bi-person-check"></i> Customer Reports</h4>
            <div class="list-group">
                <?= Html::a('Customer by Sales', ['/sales/report/customer-by-sales'], ['class' => 'list-group-item list-group-item-action']) ?>
            </div>
        </div>
    </div>

</div>