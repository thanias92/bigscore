<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\TerbilangHelper;
use app\models\Pengaturanakun; // Pastikan model ini tersedia dan benar

$setting = Pengaturanakun::findOne(1); // Mengambil pengaturan akun, asumsi ID 1

$subTotal       = $model->sub_total ?? 1200000; // Updated based on image
$diskonPersen   = $model->diskon ?? 0.0; // Updated based on image
$pajak          = $subTotal * 0.11;
$diskonNominal  = $subTotal * ($diskonPersen / 100);
$grandTotal     = $subTotal + $pajak - $diskonNominal;

// Data perusahaan (dari model Pengaturanakun atau placeholder)
$companyName = 'PT BIGS INTEGRASI TEKNOLOGI';
$companyAddress1 = 'JL. TUANKU TAMBUSAU NO. 111, SUKAJADI, PEKANBARU, RIAU,';
$companyAddress2 = '28124';
$companyTelp = '082169578165';
$companyEmail = 'bigsteknologi@gmail.com';
$companyNPWP = '43.328.995.6-216.000'; // Contoh NPWP

// Data invoice (dari model atau placeholder)
$invoiceNumber = $model->no_faktur ?? 'INV/BIGS/062/025/16'; // Example from image
$invoiceDate = $model->purchase_date ?? '03/06/2025'; // Updated based on image
$dueDate = $model->tgl_jatuhtempo ?? '18/06/2025'; // Updated based on image

// Data pelanggan (dari model atau placeholder)
$customerName = $model->deals->customer->customer_name ?? 'Klinik Putri Yasmi Medika';
$customerAddress = $model->deals->customer->customer_address ?? 'JL. GARUDA SAKTI KM. 9 KOMPLEK, PONPES UMAR BIN KHATTAB, KEC. TAPUNG, KAB. KAMPAR';
$customerPhone = $model->deals->customer->customer_phone ?? '-'; // Assuming customer_phone exists
$customerFax = '-'; // Placeholder for FAX

// Data item (dari model atau placeholder)
$itemDescription = $model->description ?? 'Emesys Clinic - Tagihan Pemakaian Emesys Teman I (Juni 2025 - Agustus 2025)';
$itemQty = $model->deals->unit_product ?? '1 Bulan'; // Updated based on image
$itemUnitPrice = $model->deals->price_product ?? 1200000; // Updated based on image
$itemDiscount = $model->diskon ?? 0.0; // Discount percentage
$itemTax = 'X'; // As per image, tax is 'X' at line item level

// Data tanda tangan
$signerName = 'Pasman Rizky';
$signerTitle = 'Chief Operating Officer';

// URL Logo dan TTD
$logoUrl = Yii::getAlias('@webroot') . '/uploads/logo/' . ($setting->logo ?? 'default_logo.png');
$ttdUrl = Yii::getAlias('@webroot') . '/uploads/ttd/' . ($setting->ttd ?? 'default_ttd.png');

// Pastikan file gambar ada atau gunakan placeholder jika tidak
// Untuk PDF, penting untuk menggunakan path absolut atau base64 encoding
// Di sini saya asumsikan @webroot sudah dikonfigurasi dengan benar untuk akses file lokal pada server
// Jika ini untuk mPDF/Dompdf, path absolut biasanya lebih aman.
// Untuk contoh ini, saya akan menggunakan placeholder jika file tidak ditemukan secara lokal.
if (!file_exists($logoUrl)) {
    $logoUrl = 'https://placehold.co/100x60/f0f0f0/cccccc?text=Logo'; // Placeholder if file not found
}
if (!file_exists($ttdUrl)) {
    $ttdUrl = 'https://placehold.co/150x80/f0f0f0/cccccc?text=Signature'; // Placeholder if file not found
}

// Terbilang value, based on image
$terbilangText = TerbilangHelper::toTerbilang($grandTotal) ?? 'SATU JUTA TIGA RATUS TIGA PULUH DUA RIBU RUPIAH';
?>

<style>
    table.items-table,
    table.items-table th,
    table.items-table td {
        border: 1px solid #000;
        border-collapse: collapse;
        font-size: 11px;
        padding: 6px;
    }

    table.items-table th {
        background-color: #f2f2f2;
        font-weight: bold;
        text-align: center;
    }

    table.items-table td {
        vertical-align: top;
        word-break: break-word;
    }

    .items-table .text-right {
        text-align: right;
    }

    .items-table .text-center {
        text-align: center;
    }

    body {
        font-family: Arial, sans-serif;
        font-size: 11px;
        color: #000;
        margin: 0;
        padding: 0;
    }

    .invoice-container {
        width: 100%;
        margin: 0 auto;
        padding: 0;
        box-sizing: border-box;
    }

    /* Main header table for company info and logo/invoice details */
    .main-header-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 15px;
    }

    .main-header-table td {
        border: none;
        padding: 0;
        vertical-align: top;
    }

    .company-details {
        font-size: 11px;
        line-height: 1.4;
    }

    .header-logo {
        height: 60px;
        margin-bottom: 10px;
        display: block;
        /* Ensure it takes its own line */
        margin-left: auto;
        /* Center or push to right within its cell */
        margin-right: 0;
        /* Align to right */
    }

    .terbilang-box {
        border: 1px solid #000;
        padding: 6px;
        margin-bottom: 5px;
        /* Awalnya 15px */
    }

    .invoice-details-table {
        width: auto;
        /* Allow table to shrink to content */
        margin-left: auto;
        /* Push table to the right within its cell */
        margin-right: 0;
        /* Align to right */
        border-collapse: collapse;
        display: block;
        /* Make it a block element to respect margin: auto */
    }

    .invoice-details-table td {
        border: none;
        padding: 0;
        text-align: left;
    }

    .invoice-details-table td:first-child {
        font-weight: bold;
        padding-right: 5px;
    }

    .title {
        font-size: 18px;
        font-weight: bold;
        text-align: center;
        margin: 10px 0 20px 0;
        border-bottom: 2px solid #000;
        padding-bottom: 5px;
    }

    .customer-info-section {
        margin-bottom: 15px;
    }

    .customer-info-section table {
        border: 1px solid #000;
    }

    .customer-info-section td {
        border: none;
        padding: 6px;
        vertical-align: top;
    }

    .customer-info-section strong {
        display: inline-block;
        width: 60px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 11px;
        margin-bottom: 15px;
    }

    th,
    td {
        border: 1px solid #000;
        padding: 6px;
        text-align: left;
        vertical-align: top;
    }

    th {
        background-color: #f2f2f2;
        font-weight: bold;
        text-align: center;
    }

    .text-right {
        text-align: right;
    }

    .text-center {
        text-align: center;
    }

    .bold {
        font-weight: bold;
    }

    .summary-table-container {
        width: 300px;
        margin-left: auto;
        margin-top: 15px;
    }

    .summary-table {
        width: 100%;
        border-collapse: collapse;
    }

    .summary-table td {
        border: 1px solid #000;
        padding: 6px;
    }

    .summary-table .label {
        text-align: right;
        font-weight: normal;
    }

    .summary-table .value {
        text-align: right;
        font-weight: bold;
    }

    .summary-table .total-row td {
        font-weight: bold;
    }

    .payment-details-heading {
        font-weight: bold;
        margin-top: 15px;
        margin-bottom: 5px;
    }

    .bank-details-table {
        margin-bottom: 15px;
        border: 1px solid #000;
    }

    .bank-details-table td {
        border: none;
        padding: 6px;
    }

    .bank-details-table strong {
        display: inline-block;
        width: 90px;
    }

    .terbilang-heading {
        font-weight: bold;
        margin-top: 15px;
        margin-bottom: 5px;
    }

    .terbilang-box {
        border: 1px solid #000;
        padding: 6px;
        margin-bottom: 15px;
    }

    .signature-section {
        margin-top: 20px;
        margin-left: auto;
        margin-right: 0;
        width: 200px;
        display: block;
        clear: both;
        text-align: center;
        page-break-inside: avoid;
        /* Ini penting! */
    }

    .signature-section img {
        height: 80px;
        margin-bottom: 5px;
    }
</style>

<div class="invoice-container">

    <table class="main-header-table">
        <tr>
            <td style="width: 50%;">
                <div style="font-size: 14px; font-weight: bold;"><?= Html::encode($companyName) ?></div>
                <div class="company-details">
                    <div><?= Html::encode($companyAddress1) ?></div>
                    <div><?= Html::encode($companyAddress2) ?></div>
                    <div>Telp: <?= Html::encode($companyTelp) ?></div>
                    <div>Email: <?= Html::encode($companyEmail) ?></div>
                    <div><?= Html::encode($companyNPWP) ?></div>
                </div>
            </td>
            <td style="width: 50%; text-align: right;">
                <img src="<?= $logoUrl ?>" alt="Company Logo" class="header-logo"><!-- Image of Logo Perusahaan -->
                <table class="invoice-details-table">
                    <tr>
                        <td>FAKTUR #</td>
                        <td>: <?= Html::encode($invoiceNumber) ?></td>
                    </tr>
                    <tr>
                        <td>TANGGAL</td>
                        <td>: <?= Html::encode($invoiceDate) ?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="title">FAKTUR</div>

    <div class="customer-info-section">
        <table>
            <tr>
                <td style="width: 50%;">
                    <strong>NAMA</strong>: <?= Html::encode($customerName) ?><br>
                    <strong>ALAMAT</strong>: <?= Html::encode($customerAddress) ?><br>
                    <strong>TELP</strong>: <?= Html::encode($customerPhone) ?><br>
                    <strong>FAX</strong>: <?= Html::encode($customerFax) ?>
                </td>
                <td style="width: 50%;">
                    <strong>JATUH TEMPO</strong>: <?= Html::encode($dueDate) ?>
                </td>
            </tr>
        </table>
    </div>
    <?php
    // Hitung total cicilan yang sudah lunas
    $bayaranDiterima = 0;

    if (!empty($model->cicilans)) {
        foreach ($model->cicilans as $cicilan) {
            if ($cicilan->status === 'Lunas') {
                $bayaranDiterima += $cicilan->nominal;
            }
        }
    }

    // Hitung sisa tagihan
    $sisaTagihan = $grandTotal - $bayaranDiterima;
    ?>
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">NO</th>
                <th style="width: 30%;">KETERANGAN</th> <!-- dikurangi dari 35% -->
                <th style="width: 10%;">QTY</th>
                <th style="width: 15%;">HARGA SATUAN</th>
                <th style="width: 10%;">DISKON</th>
                <th style="width: 10%;">PAJAK</th>
                <th style="width: 20%;">JUMLAH (Rp.)</th> <!-- ditambah dari 15% -->
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1</td>
                <td><?= Html::encode($itemDescription) ?></td>
                <td class="text-center"><?= Html::encode($itemQty) ?></td>
                <td class="text-right"><?= number_format($itemUnitPrice, 2, ',', '.') ?></td>
                <td class="text-center"><?= number_format($itemDiscount, 1, ',', '.') ?>%</td>
                <td class="text-center"><?= Html::encode($itemTax) ?></td>
                <td class="text-right"><?= number_format($subTotal, 2, ',', '.') ?></td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" class="text-right">Subtotal</td>
                <td class="text-right"><?= number_format($subTotal, 2, ',', '.') ?></td>
            </tr>
            <tr>
                <td colspan="6" class="text-right">PPN 11.0%</td>
                <td class="text-right"><?= number_format($pajak, 2, ',', '.') ?></td>
            </tr>
            <tr>
                <td colspan="6" class="text-right">TOTAL</td>
                <td class="text-right bold"><?= number_format($grandTotal, 2, ',', '.') ?></td>
            </tr>
            <tr>
                <td colspan="6" class="text-right">Bayaran Diterima</td>
                <td class="text-right"><?= number_format($bayaranDiterima, 2, ',', '.') ?></td>
            </tr>
            <tr>
                <td colspan="6" class="text-right">Sisa Tagihan</td>
                <td class="text-right"><?= number_format($sisaTagihan, 2, ',', '.') ?></td>
            </tr>
        </tfoot>
    </table>


    <div class="payment-details-heading">DETAIL PEMBAYARAN</div>
    <table class="bank-details-table">
        <tr>
            <td>
                <strong>NAMA BANK</strong>: MANDIRI<br>
                <strong>CABANG BANK</strong>: KCP PEKANBARU AULIA HOSPITAL<br>
                <strong>NOMOR AKUN BANK</strong>: 106-00-2350484-8<br>
                <strong>ATAS NAMA</strong>: PT BIGS INTEGRASI TEKNOLOGI
            </td>
        </tr>
    </table>
    <?php if ($model->isCicilan()): ?>
        <div class="heading-text">
            RINCIAN CICILAN (<?= count($model->cicilans) ?> BULAN)
        </div>
        <table class="items-table">
            <thead>
                <tr>
                    <th class="text-center">Bulan Ke</th>
                    <th class="text-center">Jatuh Tempo</th>
                    <th class="text-center">Nominal</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($model->cicilans as $c): ?>
                    <tr>
                        <td class="text-center"><?= $c->ke ?></td>
                        <td class="text-center"><?= Yii::$app->formatter->asDate($c->jatuh_tempo, 'php:d/m/Y') ?></td>
                        <td class="text-right"><?= number_format($c->nominal, 0, ',', '.') ?></td>
                        <td class="text-center"><?= $c->status === 'Lunas' ? '✔ Lunas' : 'Belum Lunas' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>


    <div class="terbilang-heading">TERBILANG</div>
    <div class="terbilang-box">
        <?= Html::encode($terbilangText) ?>
    </div>

    <div class="signature-section">
        Hormat Kami,<br><br>
        <img src="<?= $ttdUrl ?>" alt="Tanda Tangan"><!-- Image of Tanda Tangan --><br>
        <strong><?= Html::encode($signerName) ?></strong><br>
        <?= Html::encode($signerTitle) ?>
    </div>

</div>