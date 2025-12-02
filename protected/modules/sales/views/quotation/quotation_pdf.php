<?php

use yii\helpers\Html;

/** @var app\models\Quotation $model */
/** @var app\models\Pengaturanakun $setting */
/** @var string|null $logoPath */

$formatter = Yii::$app->formatter;
?>

<style>
    body {
        background-color: rgba(245, 248, 250, 1) !important;
        font-family: sans-serif;
        font-size: 12px;
        color: #333;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        padding: 8px;
        text-align: left;
        vertical-align: top;
    }

    .text-right {
        text-align: right;
    }

    .header-table td {
        padding: 0;
        border: 0;
    }

    .company-details {
        font-size: 11px;
        line-height: 1.5;
    }

    .company-details strong {
        font-size: 16px;
        color: #27465E;
    }

    .document-title h1 {
        font-size: 2.2rem;
        font-weight: bold;
        color: #27465E;
        margin: 0;
    }

    .document-title p {
        margin: 0;
        color: #555;
    }

    .header-border {
        border-bottom: 2px solid #27465E;
        padding-bottom: 1rem;
    }

    .info-table {
        margin-top: 20px;
        background-color: #f8f9fa;
        border-radius: 6px;
    }

    .info-table td {
        padding: 10px;
        line-height: 1.6;
    }

    .items-table {
        margin-top: 25px;
    }

    .items-table thead th {
        background-color: #27465E;
        color: white;
        font-weight: bold;
        text-transform: uppercase;
        font-size: 11px;
    }

    .items-table tbody tr {
        border-bottom: 1px solid #e9ecef;
    }

    .section-title {
        font-size: 1.1rem;
        font-weight: bold;
        color: #27465E;
        margin-top: 25px;
        margin-bottom: 10px;
    }

    .summary-section {
        margin-top: 20px;
    }

    .summary-table {
        width: 350px;
        float: right;
    }

    .summary-table td {
        padding: 6px;
    }

    .grand-total {
        font-weight: bold;
        font-size: 1.1rem;
        color: #27465E;
    }

    .notes-section {
        margin-top: 20px;
        font-size: 11px;
        color: #555;
    }

    .footer {
        margin-top: 30px;
        text-align: center;
        font-size: 11px;
        color: #888;
        border-top: 1px solid #e9ecef;
        padding-top: 15px;
    }
</style>

<table class="header-table">
    <tr>
        <td width="50%" class="company-details header-border">
            <strong><?= Html::encode($setting->nama_instansi ?? 'PT Bigs Integrasi Teknologi') ?></strong><br>
            <?= nl2br(Html::encode($setting->alamat_instansi ?? "Jl. Jend. Sudirman No. 123, Pekanbaru, Riau\nIndonesia, 28282")) ?><br>
            <?= Html::encode($setting->email ?? 'info@bigs.id') ?> | <?= Html::encode($setting->telepon ?? '+62 812 7601 7962') ?>
        </td>
        <td width="50%" class="text-right header-border">
            <?php if ($logoPath): ?>
                <img src="<?= $logoPath ?>" style="max-width: 150px; height: auto;">
            <?php endif; ?>
            <h1>QUOTATION</h1>
            <p><?= Html::encode($model->quotation_code) ?></p>
        </td>
    </tr>
</table>

<table class="info-table">
    <tr>
        <td width="50%">
            <strong>BILLED TO:</strong><br>
            <?= Html::encode($model->customer->customer_name ?? 'N/A') ?><br>
            <?= Html::encode($model->customer->customer_email ?? 'N/A') ?>
        </td>
        <td width="50%" class="text-right">
            <strong>Date of Issue:</strong> <?= $formatter->asDate($model->created_date, 'long') ?><br>
            <strong>Expiration Date:</strong> <?= $formatter->asDate($model->expiration_date, 'long') ?>
        </td>
    </tr>
</table>

<div class="section-title">Order Lines</div>
<table class="items-table">
    <thead>
        <tr>
            <th>Item Description</th>
            <th class="text-right" width="10%">Qty</th>
            <th class="text-right" width="20%">Unit Price</th>
            <th class="text-right" width="20%">Amount</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                <strong><?= Html::encode($model->product->product_name ?? 'N/A') ?></strong>
                <?php if (!empty($model->description)): ?>
                    <p style="font-size: 11px; color: #666; margin-top: 5px;"><?= nl2br(Html::encode($model->description)) ?></p>
                <?php endif; ?>
            </td>
            <td class="text-right"><?= Html::encode($model->unit_product) ?></td>
            <td class="text-right"><?= 'Rp' . number_format($model->price_product, 2, ',', '.') ?></td>
            <td class="text-right"><?= 'Rp' . number_format($model->total, 2, ',', '.') ?></td>
        </tr>
    </tbody>
</table>

<?php if ($model->optional_product_id): ?>
    <div class="section-title">Optional Products</div>
    <table class="items-table">
        <thead>
            <tr>
                <th>Item Description</th>
                <th class="text-right" width="10%">Qty</th>
                <th class="text-right" width="20%">Unit Price</th>
                <th class="text-right" width="20%">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong><?= Html::encode($model->optionalProduct->product_name ?? 'N/A') ?></strong></td>
                <td class="text-right"><?= Html::encode($model->optional_unit_product) ?></td>
                <td class="text-right"><?= 'Rp' . number_format($model->optional_price_product, 2, ',', '.') ?></td>
                <td class="text-right"><?= 'Rp' . number_format($model->optional_total, 2, ',', '.') ?></td>
            </tr>
        </tbody>
    </table>
<?php endif; ?>

<!-- === PERUBAHAN TOTAL DI SINI: GUNAKAN TABEL PEMBUNGKUS === -->
<table style="width: 100%; margin-top: 20px;">
    <!-- BARIS PERTAMA: Untuk Summary -->
    <tr>
        <!-- Kolom kosong di kiri untuk mendorong summary ke kanan -->
        <td style="width: 60%; border: none;">&nbsp;</td>

        <!-- Kolom kanan yang berisi tabel summary -->
        <td style="width: 40%; border: none; vertical-align: top;">
            <table class="summary-table">
                <tr>
                    <td>Subtotal</td>
                    <td align="right"><?= 'Rp' . number_format($model->total, 2, ',', '.') ?></td>
                </tr>
                <tr>
                    <td>Tax (11%)</td>
                    <td align="right"><?= 'Rp' . number_format($model->total * 0.11, 2, ',', '.') ?></td>
                </tr>
                <tr>
                    <td class="grand-total">GRAND TOTAL</td>
                    <td class="grand-total" align="right"><?= 'Rp' . number_format($model->total * 1.11, 2, ',', '.') ?></td>
                </tr>
            </table>
        </td>
    </tr>
    <!-- BARIS KEDUA: Untuk Notes & Terms -->
    <tr>
        <!-- Kolom ini membentang penuh untuk Notes -->
        <td colspan="2" style="border: none; padding-top: 20px;">
            <div class="notes-section">
                <strong>Notes & Terms:</strong>
                <p style="margin-top: 5px;">
                    1. Payment should be made to the bank account specified above.<br>
                    2. This quotation is valid until the expiration date mentioned.<br>
                    3. Please mention the quotation number in your payment reference.
                </p>
            </div>
        </td>
    </tr>
</table>
<!-- === AKHIR PERUBAHAN === -->

<div class="footer">
    Thank you for your business! We look forward to working with you.
</div>