<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\grid\ActionColumn;
use yii\web\View;
use yii\bootstrap5\Modal;
use app\widgets\JSRegister;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\vendorfinance\PemasukanSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Pemasukan');
$this->params['breadcrumbs'][] = $this->title;
?>
<?php
$css = <<<CSS
.modal-dialog.modal-superwide {
    max-width: 80% !important;
    width: 80% !important;
}
.modal-body {
    max-height: 85vh;
    overflow-y: auto;
}
.btn.btn-primary {
    background-color: #27465E;
    border-color: #27465E;
  }

  .btn.btn-primary:hover {
    background-color: #1f384d;
    /* warna sedikit lebih gelap untuk hover */
    border-color: #1f384d;
  }

  .btn-secondary {
    background-color: transparent !important;
    border: 1px solid #E1E3EA !important;
    color: #5E6278 !important;
  }

  .btn-secondary:hover {
    background-color: rgba(225, 227, 238, 0.1);
    /* efek hover ringan */
    border-color: #E1E3EA;
    color: #5E6278;
  }

  .btn-light-custom {
    background-color: #E5E7EB;
    border: 1px solid #C0BFC0;
    color: #191919;
    /* Warna teks tombol */
  }

  .btn-light-custom:hover {
    background-color: #D1D4D9;
    /* Efek hover, sedikit lebih gelap dari background */
    border-color: #A3A9B3;
    /* Warna border saat hover */
  }

  /* CSS untuk GridView */
  .table-sm {
    background-color: #FFFFFF;
    /* Warna latar belakang tabel */
    border: 1px solid #C0BFC0;
    /* Warna border tabel */
  }

  .table-sm th {
    background-color: #E5E7EB;
    /* Warna latar belakang header */
    color: #3F4254;
    /* Warna tulisan header */
    border: 1px solid #C0BFC0;
    /* Warna border header */
  }

  .table-sm td {
    background-color: #FFFFFF;
    /* Warna latar belakang sel */
    color: #191919;
    /* Warna tulisan sel */
    border: 1px solid #C0BFC0;
    /* Warna border sel */
  }

  .table-sm tr:nth-child(even) td {
    background-color: #f8f9fa;
    /* Warna baris genap */
  }

  .table-sm tr:nth-child(odd) td {
    background-color: #ffffff;
    /* Warna baris ganjil */
  }
CSS;
$this->registerCss($css);
?>
<?php yii\bootstrap5\Modal::begin([
  'headerOptions' => ['id' => 'modalHeader'],
  'id' => 'modal',
  'size' => 'modal-xl',
  'options' => [
    'data-bs-backdrop' => 'static',
    'data-bs-keyboard' => 'false',
    'tabindex' => false,
  ],
]);
echo "<div id='modalContent'></div>";
yii\bootstrap5\Modal::end();
?>

<div class="card">
  <div class="card-header d-flex">
    <div class="header-title flex-grow-1">
      <h4 class="card-title">Pemasukan</h4>
    </div>
    <div style="width:20%">
      <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    </div>
    <div class="ms-3">
      <?php // echoHtml::a('<i class="fa fa-search"></i> Cari', '#pemasukan-search', ['class' => 'btn btn-sm btn-warning', 'data-bs-toggle' => 'collapse' , 'aria-controls' => 'pemasukan-search']); 
      ?>
      <?= Html::button( ' <i class="fas fa-plus"></i> Add', ['value' => Url::to(['create']), 'title' => 'Form ' . Yii::t('app', 'Pemasukan'), 'class' => 'showModalButton btn btn-primary']); ?>
    </div>
  </div>
  <div class="card-body">
    <div class="pemasukan-index col-lg-12 p-0 table-responsive">
      <?php Pjax::begin(['id' => 'gridDatapemasukan', 'timeout' => false, 'enablePushState' => false, 'enableReplaceState' => false]); ?>
      <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'pjax' => true,
        'tableOptions' => ['class' => 'table-sm'],
        'bordered' => false,
        'hover' => true,
        // 'filterModel' => $searchModel,
        'columns' => [
          ['class' => 'yii\grid\SerialColumn'],
          [
            'attribute' => 'purchase_date',
            'label' => 'Purchase Date',
            'format' => 'raw',
            'contentOptions' => ['class' => 'text-center'],
            'headerOptions'  => ['class' => 'text-center'],
            'value' => function ($model) {
              if (!$model->purchase_date) return '-';

              return Html::a(
                Yii::$app->formatter->asDate($model->purchase_date),
                '#',
                [
                  'value' => Url::to(['view', 'pemasukan_id' => $model->pemasukan_id]),
                  'title' => 'Detail ' . Yii::t('app', 'Pemasukan'),
                  'class' => 'showModalButton text-decoration-underline text-primary',
                ]
              );
            },
          ],

          [
            'attribute' => 'no_faktur',
            'format' => 'raw',
            'contentOptions' => ['class' => 'text-center'],
            'headerOptions'  => ['class' => 'text-center'],
            'value' => function ($model) {
              return Html::a($model->no_faktur, '#', [
                'class' => 'text-decoration-underline text-primary fw-bold btn-show-update-modal',
                'data-id' => $model->pemasukan_id,
                'data-url' => \yii\helpers\Url::to(['update', 'pemasukan_id' => $model->pemasukan_id, 'edit' => 1]),
                'title' => 'Edit & Terbit Faktur',
              ]);
            },
          ],
          [
            'label' => 'Customer Name',
            'contentOptions' => ['class' => 'text-center'],
            'headerOptions'  => ['class' => 'text-center'],
            'value' => function ($model) {
              return $model->deals && $model->deals->customer
                ? $model->deals->customer->customer_name
                : '-';
            },
          ],
          [
            'attribute' => 'tgl_jatuhtempo',
            'label' => 'Due date',
            'format' => 'raw',
            'contentOptions' => ['class' => 'text-center'],
            'headerOptions'  => ['class' => 'text-center'],
            'value' => function ($model) {
              if (!$model->tgl_jatuhtempo) return '-';

              $jatuhTempo = new \DateTime($model->tgl_jatuhtempo);
              $hariIni = new \DateTime();
              $interval = $hariIni->diff($jatuhTempo);
              $selisih = (int)$hariIni->format('Ymd') <= (int)$jatuhTempo->format('Ymd')
                ? $interval->days . ' days left'
                : 'past  ' . $interval->days . ' days';

              return Yii::$app->formatter->asDate($model->tgl_jatuhtempo) . "<br><small class='text-muted'>($selisih)</small>";
            },
          ],
          [
            'label'  => 'installment Progress',
            'format' => 'raw',
            'contentOptions' => ['class' => 'text-center'],
            'headerOptions'  => ['class' => 'text-center'],
            'value'  => function ($model) {
              if ($model->cicilan == 0 || empty($model->cicilans)) {
                return Html::tag('span', 'Tidak Ada Cicilan', ['class' => 'btn btn-sm btn-secondary']);
              }

              $ke = array_map(function ($c) {
                return $c->ke;
              }, $model->cicilans);

              if (!empty($ke)) {
                return Html::tag('span', '' . implode(', ', $ke), ['class' => 'btn btn-sm btn-primary rounded']);
              }

              return Html::tag('span', '-', ['class' => 'btn btn-sm btn-warning']);
            },
          ],
          [
            'attribute' => 'sisa_tagihan',
            'label'     => 'Remaining Bill (per Installment)',
            'format'    => 'raw',
            'contentOptions' => ['class' => 'text-center'],  // <--- Rata tengah isi kolom
            'headerOptions'  => ['class' => 'text-center'],  // <--- Rata tengah judul kolom
            'value'     => function ($m) {
              $total = $terbayar = 0;
              foreach ($m->cicilans as $c) {
                $total += $c->nominal;
                if ($c->status === 'Lunas') $terbayar += $c->nominal;
              }

              if (!$m->cicilan) {
                return 'Rp ' . number_format($m->sisa_tagihan, 0, ',', '.') .
                  ($m->sisa_tagihan ? '' : '<br><small>Paid Off</small>');
              }

              $sisaNom = $total - $terbayar;
              $sisaBln = $m->cicilan - count(array_filter($m->cicilans, fn($c) => $c->status === 'Lunas'));

              return 'Rp ' . number_format($sisaNom, 0, ',', '.') .
                "<br><small>Remaining  {$sisaBln} Installments</small>";
            },
          ],
          [
            'attribute' => 'status',
            'format' => 'raw', // penting agar HTML ditampilkan
            'contentOptions' => ['class' => 'text-center'],
            'headerOptions'  => ['class' => 'text-center'],
            'value' => function ($model) {
              $label = $model->statusLabel;

              switch ($label) {
                case 'Lunas':
                  $bg = '#28a7451a'; // Hijau muda transparan (10% opacity)
                  $color = '#28a745'; // Hijau
                  break;
                case 'Telat Bayar':
                  $bg = '#AB31291A';
                  $color = '#AB3129';
                  break;
                case 'Menunggu Pembayaran':
                default:
                  $bg = '#4881AD1A';
                  $color = '#4881AD';
                  break;
              }

              return Html::tag('span', $label, [
                'style' => "
                        display: inline-block;
                        padding: 6px 12px;
                        border-radius: 8px;
                        background-color: {$bg};
                        color: {$color};
                        font-weight: bold;
                    "
              ]);
            },
          ],
          [
            'header' => 'Actions',
            'format' => 'raw',
            'contentOptions' => ['class' => 'text-center'],
            'headerOptions'  => ['class' => 'text-center'],
            'value' => function ($model) {
              $cicilans = \app\models\PemasukanCicilan::find()
                ->where(['pemasukan_id' => $model->pemasukan_id])
                ->orderBy(['ke' => SORT_ASC])
                ->all();

              $maxLunas = 0;
              // $nextCicilan = null;

              foreach ($cicilans as $cicilan) {
                if ($cicilan->status === 'Lunas') {
                  if ($cicilan->ke > $maxLunas) {
                    $maxLunas = $cicilan->ke;
                  }
                }
              }

              $nextKe = $maxLunas + 1;

              $nextCicilan = \app\models\PemasukanCicilan::find()
                ->where([
                  'pemasukan_id' => $model->pemasukan_id,
                  'ke' => $nextKe,
                ])
                ->one();

              $invoiceSebelumnya = '';
              if ($nextKe > 1) {
                $sebelumnya = \app\models\PemasukanCicilan::find()
                  ->where(['pemasukan_id' => $model->pemasukan_id, 'ke' => $nextKe - 1])
                  ->one();

                if ($sebelumnya && $sebelumnya->status !== 'Lunas') {
                  $pemasukanSebelumnya = \app\models\Pemasukan::findOne($sebelumnya->pemasukan_id);
                  $invoiceSebelumnya = $pemasukanSebelumnya ? $pemasukanSebelumnya->no_faktur : '';
                }
              }

              return Html::button('Terima Pembayaran', [
                'class' => 'btn btn-sm btn-success btn-terima-pembayaran',
                'data-id' => $model->pemasukan_id,
                'data-url' => Url::to(['penerimaan-pembayaran', 'id' => $model->pemasukan_id]),
                'data-cicilan-ke' => $nextKe,
                'data-max-lunas' => $maxLunas,
                'data-invoice-sebelumnya' => $invoiceSebelumnya,
              ]);
            }

          ],

        ],
      ]); ?>


    </div>
  </div>
</div>

<?php Pjax::end(); ?>

<?php Modal::begin([
  'id' => 'modal-terima-pembayaran',
  'title' => '<h5>Terima Pembayaran</h5>',
  'dialogOptions' => ['class' => 'modal-dialog modal-superwide'],
  'scrollable' => true,
  'options' => [
    'tabindex' => false,
  ],
]); ?>
<div id="modal-content-terima-pembayaran">
  <div class="text-center p-5">
    <div class="spinner-border text-primary"></div>
  </div>
</div>
<?php Modal::end(); ?>

<?php
// Modal untuk update pemasukan
Modal::begin([
  'id' => 'modal-update-pemasukan',
  'title' => '<h5>Update Pemasukan</h5>',
  'size' => 'modal-xl', // ubah dari 'modal-lg' ke 'modal-xl'
  'scrollable' => true,
  'options' => [
    'tabindex' => false, // agar bisa input select2 di modal
  ],
]);
?>
<div id="modal-content-update-pemasukan">
  <div class="text-center p-5">
    <div class="spinner-border text-primary"></div>
  </div>
</div>
<?php Modal::end(); ?>

<?php
$link_delete = Url::to(['delete']);
$link_generate_faktur = Url::to(['generate-faktur']);

JSRegister::begin(['position' => View::POS_END]);
?>

<script>
  let is_fetch = false;
  let timer = null;
  let lastQuery = '';

  $(document).on('input', '#search-input', function() {
    lastQuery = $(this).val();
    clearTimeout(timer);

    if (lastQuery.length >= 3 || lastQuery.length === 0) {
      timer = setTimeout(() => {
        $.pjax.reload({
          container: '#gridDatapemasukan',
          url: '<?= Url::to(['index']) ?>?PemasukanSearch[queryString]=' + encodeURIComponent(lastQuery),
          replace: false,
          push: false,
          timeout: 3000, // amankan waktu timeout
        });
      }, 400);
    }
  });

  // Tetap update nilai input setelah reload
  $(document).on('pjax:end', function() {
    $('#search-input').val(lastQuery);
  });


  $(document).on('submit', '.pemasukan-search form', function(e) {
    e.preventDefault(); // cegah reload
  });

  $(document).on('click', '.btn-terima-pembayaran', function() {
    const url = $(this).data('url');
    const nextKe = parseInt($(this).data('cicilan-ke')); // misal 2
    const maxLunas = parseInt($(this).data('max-lunas')); // harusnya 1
    const invoiceSebelumnya = $(this).data('invoice-sebelumnya');

    console.log('Next cicilan ke:', nextKe);
    console.log('Max Lunas cicilan ke:', maxLunas);

    if (nextKe > maxLunas + 1) {
      Swal.fire({
        icon: 'warning',
        title: 'Cicilan Sebelumnya Belum Dibayar',
        html: `Silakan lunasi <b>cicilan ke-${maxLunas + 1}</b> terlebih dahulu.<br>No Invoice: <b>${invoiceSebelumnya}</b>.`,
      });
      return;
    }

    // Modal loading
    $('#modal-terima-pembayaran').modal('show');
    $('#modal-content-terima-pembayaran').html('<div class="text-center p-5"><div class="spinner-border text-primary"></div></div>');

    $.get(url, function(response) {
      $('#modal-content-terima-pembayaran').html(response);
    });
  });

  $(document).on('click', '.btn-show-update-modal', function(e) {
    e.preventDefault();
    const url = $(this).data('url');

    $('#modal-update-pemasukan').modal('show');
    $('#modal-content-update-pemasukan').html('<div class="text-center p-5"><div class="spinner-border text-primary"></div></div>');

    $.get(url, function(response) {
      $('#modal-content-update-pemasukan').html(response);
    });
  });

  $(document).on("click", ".delete", function() {
    var id = $(this).attr("pemasukan_id");
    var link_delete = "<?= $link_delete; ?>?pemasukan_id=" + id;
    Swal.fire({
      title: 'Konfirmasi',
      text: 'Apakah Akan Menghapus Data?',
      showCancelButton: true,
      confirmButtonText: 'Lanjutkan',
      cancelButtonText: 'Batal',
      icon: 'question',
      customClass: {
        confirmButton: 'my-confirm-button-class'
      },
    }).then((result) => {
      if (result.isConfirmed) {
        if (is_fetch == false) {
          is_fetch = true;
          $.ajax({
            type: "POST",
            url: link_delete,
            dataType: "html",
            success: function(response) {
              const backRes = JSON.parse(response);
              if (backRes.status == 'success') {
                Swal.fire('Ok', backRes.message, 'success');
                $.pjax.reload({
                  container: "#gridDatapemasukan"
                });
              } else {
                Swal.fire('Terjadi Kesalahan.', backRes.message, 'warning');
              }
              is_fetch = false
            }
          });
        }
      }
    })
  });
</script>
<?php JSRegister::end(); ?>

<!-- <div class="modal fade" id="modal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
    </div>
  </div>
</div> -->