<?php
$isPrint = Yii::$app->controller->action->id === 'print-invoice';

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\components\TerbilangHelper;
use app\models\Pengaturanakun;
use app\models\Pemasukan;
use app\models\PemasukanCicilan;
use app\models\PenerimaanPembayaran;

$setting = Pengaturanakun::findOne(1);

$this->title = 'Invoice ' . $model->no_faktur;
$this->params['breadcrumbs'][] = ['label' => 'Pemasukan', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// --- Data for the invoice (using existing model properties or placeholders) ---
$companyName = 'PT BIGS INTEGRASI TEKNOLOGI';
$companyAddress1 = 'JL. TUANKU TAMBUSAU NO. 111, SUKAJADI, PEKANBARU, RIAU,';
$companyAddress2 = '28124';
$companyTelp = '082169578165';
$companyEmail = 'bigsteknologi@gmail.com';
$companyNPWP = '43.328.995.6-216.000'; // Assuming this is NPWP

$invoiceNumber = $model->no_faktur ?? 'INV/BIGS/062/025/16'; // Example from image
$invoiceDate = $model->purchase_date ?? date('Y-m-d'); // Use purchase_date, fallback to current date

$customerName = $model->deals->customer->customer_name ?? 'Klinik Putri Yasmi Medika';
$customerAddress = $model->deals->customer->customer_address ?? 'JL. GARUDA SAKTI KM. 9 KOMPLEK, PONPES UMAR BIN KHATTAB, KEC. TAPUNG, KAB. KAMPAR';
$customerPhone = $model->deals->customer->customer_phone ?? '-'; // Assuming customer_phone exists
$customerFax = '-'; // Placeholder for FAX

$dueDate = $model->tgl_jatuhtempo ?? date('Y-m-d', strtotime('+15 days')); // Use tgl_jatuhtempo, fallback to 15 days from now

// Item details
$itemDescription = $model->description ?? 'Emesys Clinic - Tagihan Pemakaian Emesys Teman I (Juni 2025 - Agustus 2025)';
$itemQty = $model->deals->unit_product ?? '1 Bulan'; // Assuming unit could be '1 Bulan'
$itemUnitPrice = $model->deals->price_product ?? 1200000;
$itemDiscountPercent = $model->diskon ?? 0.0; // Assuming discount is a percentage
$itemTaxPercent = 11; // Fixed 11% tax as per image

// Calculations for the table line item
$itemSubtotal = $itemQty === '1 Bulan' ? $itemUnitPrice : ($itemUnitPrice * (int)$itemQty); // Adjust subtotal based on qty type
$itemDiscountAmount = $itemSubtotal * ($itemDiscountPercent / 100);
// The image shows 'PAJAK' as 'X' for the line item, implying it's applied at summary.
// For now, we'll keep it as 'X' in the table and calculate tax on summary.
$itemTotal = $itemSubtotal - $itemDiscountAmount; // Line item total before overall tax

// Summary calculations (from the original footer logic, adjusted for image values)
$subTotalSummary = $model->sub_total ?? $itemTotal; // Use model sub_total if available, else itemTotal
$pajakSummary = $subTotalSummary * ($itemTaxPercent / 100);
$diskonNominalSummary = $subTotalSummary * ($itemDiscountPercent / 100); // Assuming overall discount is same as item discount
$grandTotal = $subTotalSummary + $pajakSummary - $diskonNominalSummary;

$isParent = $model->parent_id === null;

$pembayaranSebelumnya = 0;
$pembayaranSaatIni = 0;
$sisaTagihan = 0;
$totalBayaran = 0;

if ($isParent) {
    // Jika parent, ambil semua cicilan yang sudah lunas dari anak-anak
    $childCicilan = PemasukanCicilan::find()
        ->alias('pc')
        ->join('LEFT JOIN', ['p' => Pemasukan::tableName()], 'pc.pemasukan_id = p.pemasukan_id')
        ->where(['p.parent_id' => $model->pemasukan_id])
        ->andWhere(['pc.status' => 'Lunas'])
        ->andWhere(['p.deleted_at' => null])
        ->all();

    foreach ($childCicilan as $cicilan) {
        $pembayaranSebelumnya += $cicilan->nominal;
    }

    $sisaTagihan = $grandTotal - $pembayaranSebelumnya;
    $pembayaranSaatIni = 0; // Untuk parent tidak menampilkan pembayaran cicilan berjalan
} else {
    // Jika child (cicilan), maka:
    $parent = Pemasukan::findOne($model->parent_id);

    if ($parent) {
        // Ambil semua cicilan lunas sebelum yang ini
        $semuaCicilan = Pemasukan::find()
            ->where(['parent_id' => $model->parent_id])
            ->orderBy(['purchase_date' => SORT_ASC])
            ->all();

        foreach ($semuaCicilan as $child) {
            if ($child->pemasukan_id === $model->pemasukan_id) {
                // Saat sudah sampai invoice saat ini, stop penjumlahan
                break;
            }

            $cicilan = PemasukanCicilan::findOne(['pemasukan_id' => $child->pemasukan_id, 'status' => 'Lunas']);
            if ($cicilan) {
                $pembayaranSebelumnya += $cicilan->nominal;
            }
        }

        // Nominal cicilan saat ini (jika sudah lunas)
        $cicilanSekarang = PemasukanCicilan::findOne(['pemasukan_id' => $model->pemasukan_id]);
        $pembayaranSaatIni = ($cicilanSekarang && $cicilanSekarang->status === 'Lunas') ? $cicilanSekarang->nominal : 0;
        $pembayaranSaatIni = $model->jumlah_pembayaran ?? 0; // ✅ tambahkan ini
        $totalBayaran = $pembayaranSebelumnya + $pembayaranSaatIni;
        $sisaTagihan = $parent->grand_total - $totalBayaran;
    }
}
// Payment details
$pembayaran = 0;
if ($model->cicilan) {
    foreach ($model->cicilans as $c) {
        if ($c->status === 'Lunas') {
            $pembayaran += $c->nominal;
        }
    }
} elseif ($model->bukti_bayar_path) {
    $pembayaran = $grandTotal; // If there's a payment proof, assume full payment for simplicity
}

// From image, it seems Bayaran Diterima is always the grand total if paid
if (isset($model->status) && $model->status === 'Lunas') { // Assuming a 'status' field for the invoice
    $pembayaran = $grandTotal;
} else {
    // If not paid, check cicilans or bukti_bayar_path
    if (isset($model->cicilan)) {
        foreach ($model->cicilans as $c) {
            if ($c->status === 'Lunas') {
                $pembayaran += $c->nominal;
            }
        }
    } elseif (isset($model->bukti_bayar_path)) {
        $pembayaran = $grandTotal; // If there's a payment proof, assume full payment for simplicity
    }
}
$cicilanModel = \app\models\PemasukanCicilan::findOne($model->cicilan);
$cicilanKe = '-';
$totalCicilan = '-';
$bayaranSebelumnya = 0;
$pembayaranSaatIni = $model->jumlah_pembayaran ?? 0; // ✅ tambahkan ini

if ($cicilanModel) {
    $cicilanKe = $cicilanModel->urutan_cicilan;
    $totalCicilan = $cicilanModel->total_cicilan;

    $bayaranSebelumnya = \app\models\PemasukanCicilan::find()
        ->joinWith('penerimaanPembayaran')
        ->where(['deal_id' => $model->deal_id])
        ->andWhere(['<', 'urutan_cicilan', $cicilanModel->urutan_cicilan])
        ->sum('penerimaan_pembayaran.jumlah_pembayaran');
}

$listCicilan = [];

if ($isParent) {
    $listCicilan = Pemasukan::find()
        ->where(['parent_id' => $model->pemasukan_id])
        ->andWhere(['deleted_at' => null])
        ->orderBy(['purchase_date' => SORT_ASC])
        ->all();
} elseif ($model->parent_id) {
    $listCicilan = Pemasukan::find()
        ->where(['parent_id' => $model->parent_id])
        ->andWhere(['deleted_at' => null])
        ->orderBy(['purchase_date' => SORT_ASC])
        ->all();
}

// penggunaan misalnya seperti ini:
// $sisaTagihan = ($model->total ?? 0) - ($bayaranSebelumnya + $pembayaranSaatIni);

// Terbilang (word representation of total)
$terbilangTotal = TerbilangHelper::toTerbilang($grandTotal) ?? 'SATU JUTA TIGA RATUS TIGA PULUH DUA RIBU RUPIAH'; // Placeholder from image

// Signature details
$signerName = 'Pasman Rizky';
$signerTitle = 'Chief Operating Officer';
?>

<style>
    body {
        font-family: Arial, sans-serif;
        font-size: 11px;
        color: #000;
        background-color: #fff;
        margin: 0;
        /* Remove default body margin */
        padding: 0;
        /* Remove default body padding */
    }

    .invoice-container {
        width: 800px;
        /* Fixed width as per print layout */
        margin: 0 auto;
        padding: 20px;
        background-color: #fff;
        box-sizing: border-box;
        border: none;
        /* No border for the container */
        box-shadow: none;
        /* No shadow for print */
    }

    .header-info-table,
    .customer-info-table,
    .items-table,
    .payment-summary-table,
    .bank-details-table,
    .terbilang-table,
    .signature-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 10px;
        /* Reduced margin for tighter layout */
        font-size: 11px;
    }

    .header-info-table td {
        padding: 0;
        /* No padding for header table cells */
        vertical-align: top;
    }

    .header-info-table .company-details {
        font-size: 11px;
        line-height: 1.4;
    }

    .header-info-table .invoice-details {
        font-size: 11px;
        text-align: right;
    }

    .header-info-table .invoice-details td {
        padding: 2px 0;
    }

    .header-info-table .invoice-details td:first-child {
        font-weight: bold;
        padding-right: 5px;
    }

    .title-section {
        font-size: 18px;
        font-weight: bold;
        text-align: center;
        margin: 10px 0 15px 0;
        /* Adjusted margin */
        border-bottom: 2px solid #000;
        padding-bottom: 5px;
    }

    .customer-info-table td {
        border: 1px solid #000;
        padding: 4px;
        vertical-align: top;
    }

    .customer-info-table strong {
        display: inline-block;
        width: 60px;
        /* Align labels */
    }

    .items-table th,
    .items-table td {
        border: 1px solid #000;
        padding: 4px;
        text-align: left;
    }

    .items-table th {
        background-color: #f2f2f2;
        font-weight: bold;
        text-align: center;
        /* Center headers */
    }

    .items-table td {
        vertical-align: top;
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

    .payment-summary-table {
        width: 300px;
        /* Fixed width for summary table */
        margin-left: auto;
        /* Align to right */
        margin-top: 10px;
    }

    .payment-summary-table td {
        border: 1px solid #000;
        padding: 4px;
    }

    .payment-summary-table .label {
        text-align: right;
        font-weight: normal;
        /* Labels are not bold in image */
    }

    .payment-summary-table .value {
        text-align: right;
        font-weight: bold;
        /* Values are bold in image */
    }

    .payment-summary-table .total-row td {
        font-weight: bold;
    }

    /* New style for the heading text */
    .heading-text {
        font-weight: bold;
        margin-bottom: 5px;
        /* Space between heading and table */
        margin-top: 15px;
        /* Space above heading */
    }

    .bank-details-table td {
        border: 1px solid #000;
        padding: 4px;
    }

    .bank-details-table strong {
        display: inline-block;
        width: 90px;
        /* Align labels */
    }

    .terbilang-table {
        /* Apply border to the table itself */
        border: 1px solid #000;
        margin-top: 10px;
        /* Space after bank details */
    }

    .terbilang-table td {
        border: none;
        /* Remove inner cell borders if the table itself has a border */
        padding: 4px;
    }

    .signature-section {
        margin-top: 20px;
        overflow: hidden;
    }

    .signature-right {
        float: right;
        width: 200px;
        /* Adjust width as needed */
        text-align: center;
    }

    .signature-right img {
        height: 80px;
        /* Adjust height as needed */
        margin-bottom: 5px;
    }

    @media print {
        body {
            background-color: #fff !important;
        }

        .btn,
        .dropdown-menu,
        .dropdown-toggle,
        #btn-input-payment,
        form,
        .btn-group,
        .alert,
        .input-group {
            display: none !important;
        }

        .invoice-container {
            box-shadow: none !important;
            margin: 0 !important;
            width: 100% !important;
            padding: 0 !important;
        }

        .header-logo img,
        .signature-right img {
            display: inline-block !important;
        }

        .table-cicilan td {
            text-align: center;
        }

        .items-table tbody td {
            text-align: center;
        }
    }

    @page {
        margin: 20mm;
    }
</style>

<div class="invoice-container">
    <table class="header-info-table">
        <tr>
            <td style="width: 60%;">
                <div style="font-size: 20px; font-weight: bold;"><?= Html::encode($companyName) ?></div>
                <div class="company-details">
                    <div><?= Html::encode($companyAddress1) ?></div>
                    <div><?= Html::encode($companyAddress2) ?></div>
                    <div>Telp: <?= Html::encode($companyTelp) ?></div>
                    <div>Email: <?= Html::encode($companyEmail) ?></div>
                    <div><?= Html::encode($companyNPWP) ?></div>
                </div>
            </td>
            <td style="width: 40%; text-align: right;">
                <img src="<?= Yii::getAlias('@web') . '/uploads/logo/' . ($setting->logo ?? 'default_logo.png') ?>" alt="Logo" style="height: 60px;">
                <table class="invoice-details" style="margin-left: auto; margin-top: 10px;">
                    <tr>
                        <td>FAKTUR #</td>
                        <td>: <?= Html::encode($invoiceNumber) ?></td>
                    </tr>
                    <tr>
                        <td>TANGGAL</td>
                        <td>: <?= Yii::$app->formatter->asDate($invoiceDate, 'php:d/m/Y') ?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="title-section">FAKTUR</div>

    <table class="customer-info-table">
        <tr>
            <td style="width: 50%;">
                <strong>NAMA</strong>: <?= Html::encode($customerName) ?><br>
                <strong>ALAMAT</strong>: <?= Html::encode($customerAddress) ?><br>
                <strong>TELP</strong>: <?= Html::encode($customerPhone) ?><br>
                <strong>FAX</strong>: <?= Html::encode($customerFax) ?>
            </td>
            <td style="width: 50%; vertical-align: top;">
                <strong>JATUH TEMPO</strong>: <?= Yii::$app->formatter->asDate($dueDate, 'php:d/m/Y') ?>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">NO.</th>
                <th style="width: 40%;">KETERANGAN</th>
                <th style="width: 10%;">QTY</th>
                <th style="width: 15%;">HARGA SATUAN (Rp.)</th>
                <th style="width: 10%;">DISKON</th>
                <th style="width: 10%;">PAJAK</th>
                <th style="width: 10%;">JUMLAH (Rp.)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1</td>
                <td><?= Html::encode($itemDescription) ?></td>
                <td class="text-center"><?= Html::encode($itemQty) ?></td>
                <td class="text-right"><?= number_format($itemUnitPrice, 2, ',', '.') ?></td>
                <td class="text-center"><?= number_format($itemDiscountPercent, 1, ',', '.') ?>%</td>
                <td class="text-center">X</td>
                <td class="text-right"><?= number_format($itemTotal, 2, ',', '.') ?></td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" style="border: none;"></td>
                <td class="text-right bold">Subtotal</td>
                <td class="text-right bold"><?= number_format($subTotalSummary, 2, ',', '.') ?></td>
            </tr>
            <tr>
                <td colspan="5" style="border: none;"></td>
                <td class="text-right bold">PPN <?= number_format($itemTaxPercent, 1, ',', '.') ?>%</td>
                <td class="text-right bold"><?= number_format($pajakSummary, 2, ',', '.') ?></td>
            </tr>
            <tr>
                <td colspan="5" style="border: none;"></td>
                <td class="text-right bold">TOTAL</td>
                <td class="text-right bold"><?= number_format($grandTotal, 2, ',', '.') ?></td>
            </tr>
            <tr>
                <td colspan="5" style="border: none;"></td>
                <td class="text-right bold">Bayaran Diterima</td>
                <td class="text-right bold"><?= number_format($pembayaranSebelumnya + $pembayaranSaatIni, 2, ',', '.') ?></td>
            </tr>
            <tr>
                <td colspan="5" style="border: none;"></td>
                <td class="text-right bold">Sisa Tagihan</td>
                <td class="text-right bold"><?= number_format($sisaTagihan, 2, ',', '.') ?></td>
            </tr>
        </tfoot>
    </table>

    <div class="heading-text">
        DETAIL PEMBAYARAN
    </div>
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

    <?php if (!empty($listCicilan)): ?>
        <div class="heading-text">
            RINCIAN CICILAN (<?= count($listCicilan) ?> BULAN)
        </div>
        <table class="items-table table-cicilan">
            <thead>
                <tr>
                    <th class="text-center">Bulan Ke</th>
                    <th class="text-center">Tanggal Invoice</th>
                    <th class="text-center">Jatuh Tempo</th>
                    <th class="text-center">Nominal</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($listCicilan as $index => $cicilan): ?>
                    <?php $cicilanData = PemasukanCicilan::findOne(['pemasukan_id' => $cicilan->pemasukan_id]); ?>
                    <tr>
                        <td class="text-center"><?= $cicilanData->urutan_cicilan ?? ($index + 1) ?></td>
                        <td class="text-center"><?= Yii::$app->formatter->asDate($cicilan->purchase_date, 'php:d/m/Y') ?></td>
                        <td class="text-center"><?= Yii::$app->formatter->asDate($cicilanData->jatuh_tempo ?? $cicilan->tgl_jatuhtempo, 'php:d/m/Y') ?></td>
                        <td class="text-right"><?= number_format($cicilanData->nominal ?? 0, 0, ',', '.') ?></td>
                        <td class="text-center"><?= ($cicilanData && $cicilanData->status === 'Lunas') ? '✔ Lunas' : 'Belum Lunas' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <div class="heading-text">
        TERBILANG
    </div>
    <table class="terbilang-table">
        <tr>
            <td><?= Html::encode(TerbilangHelper::toTerbilang($grandTotal)) ?></td>
        </tr>
    </table>

    <div class="signature-section">
        <div class="signature-right">
            <?php if ($setting && $setting->ttd): ?>
                <img src="<?= Yii::getAlias('@web') . '/uploads/ttd/' . $setting->ttd ?>" alt="TTD">
            <?php else: ?>
                <!-- Placeholder for signature image if not available -->
                <img src="https://placehold.co/150x80/f0f0f0/cccccc?text=Signature" alt="Signature Placeholder">
            <?php endif; ?>
            <p style="margin: 0; font-weight: bold;"><?= Html::encode($signerName) ?></p>
            <p style="margin: 0;"><?= Html::encode($signerTitle) ?></p>
        </div>
        <div style="clear: both;"></div>
    </div>
</div>

<script>
    window.onload = function() {
        // Only trigger print if the action is 'print-invoice'
        <?php if ($isPrint): ?>
            window.print();
        <?php endif; ?>
    };
</script>