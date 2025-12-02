<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Pemasukan */
/* @var $akunPemasukanList app\models\Accountkeluar[] */
/* @var $isEdit boolean */

$this->title = Yii::t('app', 'Update Pemasukan');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Pemasukan'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$customer = $model->deals->customer ?? null;
$customerEmail = $customer->customer_email ?? '-';
?>

<div class="pemasukan-update">

    <!-- Tombol Edit -->
    <?php if (!$isEdit): ?>
        <div class="mb-3">
            <?= Html::a('✏️ Edit Data', Url::current(['edit' => 1]), ['class' => 'btn btn-warning']) ?>
        </div>
    <?php endif; ?>

    <?= $this->render('_form', [
        'model' => $model,
        'akunPemasukanList' => $akunPemasukanList,
        'isEdit' => $isEdit ?? false,
        'pembayaranSaatIni' => $pembayaranSaatIni,
    ]) ?>
</div>

<?php if ($isEdit): ?>
    <!-- Tombol Terbit Faktur -->
    <div class="container-fluid mt-3">
        <div class="d-flex justify-content-end">
            <div class="btn-group">
                <button class="btn dropdown-toggle" data-bs-toggle="dropdown"
                    style="background-color: #f44336; color: white; font-weight: 500; padding: 8px 16px; border: none; border-radius: 6px;">
                    Terbit Faktur
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <button class="dropdown-item" id="btn-print" type="button">
                            📄 Cetak Invoice
                        </button>
                    </li>
                    <?php if ($customerEmail && $customerEmail !== '-'): ?>
                        <li>
                            <a class="dropdown-item"
                                target="_blank"
                                href="https://mail.google.com/mail/?view=cm&fs=1&to=<?= $customerEmail ?>&su=Invoice%20<?= $model->no_faktur ?>&body=Yth.%20<?= urlencode($customer->customer_name ?? '-') ?>%2C%0A%0ABerikut%20kami%20lampirkan%20invoice%20pembayaran%20dengan%20nomor%20<?= $model->no_faktur ?>.%0ASilakan%20cek%20pada%20sistem%20kami%20atau%20lampiran.%0A%0ATerima%20kasih.">
                                📧 Kirim manual via Gmail
                            </a>
                        </li>
                    <?php endif; ?>
                    <li>
                        <button class="dropdown-item" id="btn-send-pdf" type="button">
                            📎 Kirim PDF dari Server
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <?php
    $sendPdfUrl = Url::to(['send-invoice', 'id' => $model->pemasukan_id]);
    $printUrl = Url::to(['print-invoice', 'id' => $model->pemasukan_id]);

    $js = <<<JS
$('#btn-print').on('click', function() {
    window.open('{$printUrl}', '_blank');
});

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
JS;

    $this->registerJs($js);
    ?>
<?php endif; ?>