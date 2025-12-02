<?php

use yii\helpers\Html;
use yii\helpers\Url; // Make sure to include Url helper

$this->title = 'TAGIHAN PELANGGAN';
$this->params['breadcrumbs'][] = ['label' => 'Pemasukan', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$deals = $model->deals;
$customer = $deals->customer ?? null;
$product = $deals->product ?? null;
$feature = $deals->feature ?? null; // Added feature based on image

$harga = $product->harga ?? 0;
$unit = $product->unit ?? 1;
$diskonPersen = $model->diskon ?? 0;
$diskonRp = ($harga * $unit) * $diskonPersen / 100;
$subTotalLineItem = ($harga * $unit);
$subTotalSummary = $model->sub_total ?? $subTotalLineItem;
$pajak = $subTotalSummary * 0.11;
$total = $subTotalSummary + $pajak - $diskonRp;

$syaratPembayaran = 'Net 15'; // Placeholder

$customerEmail = $customer->customer_email ?? '';
?>

<style>
    .btn:hover {
        background-color: #ced4da !important;
    }

    body {
        font-family: 'Inter', sans-serif;
        background-color: #f9f9f9;
    }

    .invoice-container {
        background: #ffffff;
        padding: 25px 35px;
        margin: 0 auto;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    }

    h4.title {
        font-weight: 600;
        font-size: 24px;
        margin-bottom: 25px;
        color: #2c3e50;
        text-align: center;
    }

    .invoice-section {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        margin-bottom: 30px;
    }

    .section-left,
    .section-right {
        width: 48%;
        min-width: 300px;
    }

    .form-group-inline {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        max-width: 100%;
    }

    .form-group-inline label {
        width: 140px;
        font-weight: 600;
        margin-right: 10px;
        color: #2c3e50;
        font-size: 14px;
        flex-shrink: 0;
    }

    .form-box {
        flex-grow: 1;
        background-color: #ffffff;
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        font-size: 14px;
        color: #2d3436;
        max-width: 300px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .invoice-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 30px;
    }

    .invoice-table th,
    .invoice-table td {
        border: 1px solid #dee2e6;
        padding: 10px 12px;
        text-align: center;
        font-size: 14px;
    }

    .invoice-table th {
        background-color: rgb(45, 67, 127);
        color: white;
        font-weight: bold;
    }

    .product-cell-box {
        background-color: #e9ecef;
        border-radius: 6px;
        padding: 6px 10px;
        display: inline-block;
        font-weight: 500;
        color: #34495e;
        border: 1px solid #ced4da;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .invoice-summary {
        width: 100%;
        max-width: 400px;
        margin-left: auto;
    }

    .invoice-summary td {
        padding: 8px 12px;
        font-size: 14px;
    }

    .invoice-summary .label {
        text-align: right;
        font-weight: bold;
        color: #2c3e50;
    }

    .invoice-summary .value {
        text-align: right;
    }

    .total-row td {
        font-weight: bold;
        border-top: 2px solid #2c3e50;
        background-color: #f1f3f5;
    }

    .back-button-container {
        text-align: right;
        margin-top: 30px;
    }

    .back-button {
        background-color: #6c757d;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 16px;
        font-weight: 500;
        transition: background-color 0.3s ease;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .back-button:hover {
        background-color: #5a6268;
    }

    @media (max-width: 768px) {
        .invoice-section {
            flex-direction: column;
        }

        .section-left,
        .section-right {
            width: 100%;
            min-width: unset;
        }

        .form-group-inline {
            flex-direction: column;
            align-items: flex-start;
        }

        .form-group-inline label {
            width: auto;
            margin-bottom: 5px;
        }

        .form-box {
            width: 100%;
            max-width: 100%;
        }

        .invoice-table th,
        .invoice-table td {
            padding: 8px;
            font-size: 12px;
        }

        .invoice-summary {
            max-width: 100%;
        }
    }
</style>

<div class="invoice-container">
    <h4 class="title">TAGIHAN PELANGGAN</h4>

    <div class="invoice-section">
        <div class="section-left">
            <div class="form-group-inline">
                <label>Pelanggan</label>
                <div class="form-box"><?= $customer->customer_name ?? '-' ?></div>
            </div>
            <div class="form-group-inline">
                <label>Email Pelanggan</label>
                <div class="form-box"><?= $customer->customer_email ?? '-' ?></div>
            </div>
            <div class="form-group-inline">
                <label>Alamat Pelanggan</label>
                <div class="form-box"><?= $customer->customer_address ?? '-' ?></div>
            </div>
        </div>

        <div class="section-right">
            <div class="form-group-inline">
                <label>Tanggal Transaksi</label>
                <div class="form-box"><?= Yii::$app->formatter->asDate($model->purchase_date) ?></div>
            </div>
            <div class="form-group-inline">
                <label>Jatuh Tempo</label>
                <div class="form-box"><?= Yii::$app->formatter->asDate($model->tgl_jatuhtempo) ?></div>
            </div>
            <div class="form-group-inline">
                <label>Syarat Pembayaran</label>
                <div class="form-box"><?= $syaratPembayaran ?></div>
            </div>
        </div>
    </div>

    <table class="invoice-table">
        <thead>
            <tr>
                <th>Produk</th>
                <th>Jenis Pembelian</th>
                <th>Unit</th>
                <th>Diskon (%)</th>
                <th>Diskon (Rp)</th>
                <th>Harga Transaksi</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <div class="product-cell-box"><?= $product->product_name ?? '-' ?></div>
                </td>
                <td>
                    <div class="product-cell-box"><?= $deals->purchase_type ?? '-' ?></div>
                </td>
                <td><?= $unit ?></td>
                <td><?= $diskonPersen ?>%</td>
                <td>Rp <?= number_format($diskonRp, 0, ',', '.') ?></td>
                <td>Rp <?= number_format($harga, 0, ',', '.') ?></td>
                <td>Rp <?= number_format($subTotalLineItem, 0, ',', '.') ?></td>
            </tr>
        </tbody>
    </table>

    <table class="invoice-summary">
        <tr>
            <td class="label">Sub Total</td>
            <td class="value">Rp <?= number_format($subTotalSummary, 0, ',', '.') ?></td>
        </tr>
        <tr>
            <td class="label">Pajak</td>
            <td class="value">PPN[11%] Rp <?= number_format($pajak, 0, ',', '.') ?></td>
        </tr>
        <tr>
            <td class="label">Diskon</td>
            <td class="value"><?= $diskonPersen ?>%</td>
        </tr>
        <tr class="total-row">
            <td class="label">Total</td>
            <td class="value">Rp <?= number_format($total, 0, ',', '.') ?></td>
        </tr>
    </table>

    <div class="d-flex justify-content-end align-items-center gap-3 mt-4">
        <?= Html::a('Kembali', ['index'], [
            'class' => 'btn',
            'style' => '
            background-color: #adb5bd;
            color: white;
            height: 36px;
            font-weight: 500;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
        '
        ]) ?>

        <?php
        // --- MODIFIED BUTTON FOR MODAL TRIGGER ---
        echo Html::button('Penerimaan Pembayaran', [
            'id' => 'btn-penerimaan-pembayaran', // Unique ID for JavaScript targeting
            'class' => 'btn',
            'style' => '
            background-color: #adb5bd;
            color: white;
            height: 36px;
            font-weight: 500;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
        '
        ]);
        ?>

        <div class="btn-group" style="height: 36px;">
            <button class="btn dropdown-toggle" data-bs-toggle="dropdown"
                style="
                background-color: #adb5bd;
                color: white;
                font-weight: 500;
                height: 36px;
                padding: 8px 16px;
                border: none;
                border-radius: 6px;
            ">
                Terbit Faktur
            </button>
            <ul class="dropdown-menu">
                <li><button class="dropdown-item" id="btn-print" type="button">📄 Cetak Invoice</button></li>
                <li>
                    <a class="dropdown-item"
                        target="_blank"
                        href="https://mail.google.com/mail/?view=cm&fs=1&to=<?= $customerEmail ?>&su=Invoice%20<?= $model->no_faktur ?>&body=Yth.%20<?= urlencode($customer->customer_name ?? '-') ?>%2C%0A%0ABerikut%20kami%20lampirkan%20invoice%20pembayaran%20dengan%20nomor%20<?= $model->no_faktur ?>.%0ASilakan%20cek%20pada%20sistem%20kami%20atau%20lampiran.%0A%0ATerima%20kasih.">
                        📧 Kirim manual via Gmail
                    </a>
                </li>
                <li><button class="dropdown-item" id="btn-send-pdf" type="button">📎 Kirim PDF dari Server</button></li>
            </ul>
        </div>
    </div>
</div>

<?php
$sendPdfUrl = Url::to(['send-invoice', 'id' => $model->pemasukan_id]);
$printUrl = Url::to(['print-invoice', 'id' => $model->pemasukan_id]);
$penerimaanPembayaranUrl = Url::to(['penerimaan-pembayaran', 'id' => $model->pemasukan_id]); // URL to fetch the modal content

$js = <<<JS
// Handler for Cetak Invoice (Print)
$('#btn-print').on('click', function() {
    window.open('{$printUrl}', '_blank');
});

// Handler for Kirim PDF dari Server (Send PDF)
$('#btn-send-pdf').on('click', () => {
    Swal.fire({
        title:'Kirim Invoice PDF?',
        text :'File PDF akan dikirim ke email customer.',
        icon :'question',
        showCancelButton:true,
        confirmButtonText:'Kirim'
    }).then(r=>{
        if(!r.isConfirmed) return;

        $.post('{$sendPdfUrl}')
          .done(res=>{
              const icon = res.status==='success' ? 'success' : 'error';
              Swal.fire(icon==='success'?'Berhasil':'Gagal', res.message, icon);
          })
          .fail(()=>Swal.fire('Error','Tidak dapat menghubungi server.','error'));
    });
});

// --- JavaScript for "Penerimaan Pembayaran" Button ---
$('#btn-penerimaan-pembayaran').on('click', function() {
    // Show a loading spinner inside the modal content while waiting for AJAX
    $('#modal .modal-content').html('<div class="text-center p-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
    $('#modal').modal('show'); // Show the modal

    // Fetch the form content via AJAX
    $.get('{$penerimaanPembayaranUrl}', function(data) {
        $('#modal .modal-content').html(data); // Load the content into the modal
    }).fail(function() {
        // Handle failure to load the form
        $('#modal .modal-content').html('<div class="alert alert-danger">Gagal memuat formulir penerimaan pembayaran. Silakan coba lagi.</div>');
    });
});
JS;

$this->registerJs($js);
?>

<div class="modal fade" id="modal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            </div>
    </div>
</div>