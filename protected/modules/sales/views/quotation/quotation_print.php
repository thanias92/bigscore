<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Quotation $model */

$this->title = 'Print Quotation: ' . $model->quotation_code;
$this->params['breadcrumbs'][] = ['label' => 'Quotations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    body {
        font-family: 'Inter', sans-serif;
        background-color: #f5f8fa;
        margin: 0;
        padding: 2rem;
    }

    .page-container {
        max-width: 800px;
        margin: 0 auto;
    }

    .action-bar {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }

    .action-bar .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
        padding: 0.6rem 1.2rem;
        border: none;
        border-radius: 6px;
        font-weight: 500;
        font-size: 0.9rem;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .btn-download {
        background-color: #1aa119;
        /* Warna hijau untuk download */
        color: white;
    }

    .btn-download:hover {
        background-color: #137912;
    }

    .btn-print {
        background-color: #27465E;
        color: white;
    }

    .btn-print:hover {
        background-color: #1a2e3c;
    }

    .print-container {
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    /* Sembunyikan tombol saat mencetak */
    @media print {
        body {
            background-color: white;
            padding: 0;
        }

        .no-print {
            display: none !important;
        }

        .print-container {
            box-shadow: none;
            border-radius: 0;
        }
    }
</style>

<div class="page-container">
    <div class="no-print action-bar">
        <!-- Tombol Download PDF -->
        <?= Html::a('Download PDF', ['download-pdf', 'id' => $model->quotation_id], [
            'class' => 'btn btn-download',
            'target' => '_blank'
        ]) ?>

        <!-- Tombol Print
        <button class="btn btn-print" onclick="window.print()">Print</button> -->
    </div>

    <!-- Container tampilan quotation -->
    <div class="print-container">
        <?= $this->render('quotation_pdf', [
            'model' => $model,
            'setting' => $setting,
            'logoPath' => $logoPath,
        ]) ?>
    </div>
</div>