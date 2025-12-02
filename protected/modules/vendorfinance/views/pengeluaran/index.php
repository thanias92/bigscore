<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use app\widgets\JSRegister;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\vendorfinance\PengeluaranSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Expenditure');
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .btn.btn-primary {
    background-color: #27465E;
    border-color: #27465E;
  }

  .btn.btn-primary:hover {
    background-color: #1f384d;
    /* warna sedikit lebih gelap untuk hover */
    border-color: #1f384d;
  }
</style>

<!-- ===================================================================================== -->
<!-- MODAL BESAR (create / update / view item) ------------------------------------------- -->

<?php yii\bootstrap5\Modal::begin([
  'id'           => 'modal',
  'size'         => 'modal-xl',
  'headerOptions' => ['id' => 'modalHeader'],
  'options'      => ['data-bs-backdrop' => 'static', 'data-bs-keyboard' => 'false'],
]); ?>
<div id="modalContent"></div>
<?php yii\bootstrap5\Modal::end(); ?>

<!-- ===================================================================================== -->
<!-- MODAL PREVIEW BUKTI PEMBAYARAN ------------------------------------------------------- -->

<?php yii\bootstrap5\Modal::begin([
  'id'    => 'proofModal',
  'title' => '<h5 class="modal-title">Bukti Pembayaran</h5>',
  'size'  => 'modal-lg',
]); ?>
<div id="proofModalContent" style="text-align:center"></div>
<?php yii\bootstrap5\Modal::end(); ?>

<!-- ===================================================================================== -->


<div class="card">
  <div class="card-header d-flex">
    <div class="header-title flex-grow-1">
      <h4 class="card-title">Expenditure</h4>
    </div>

    <div style="width:20%"><?= $this->render('_search', ['model' => $searchModel]); ?></div>

    <div class="ms-3">
      <?= Html::button(' <i class="fas fa-plus"></i> Add', [
        'class' => 'showModalButton btn btn-primary',
        'value' => Url::to(['create']),
        'title' => 'Expenditure Form'
      ]); ?>
    </div>
  </div>

  <div class="card-body">
    <div class="pengeluaran-index col-lg-12 p-0 table-responsive">
      <?php Pjax::begin([
        'id' => 'gridDatapengeluaran',
        'timeout' => false,
        'enablePushState' => true,
        'enableReplaceState' => false
      ]); ?>
      <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'pjax' => true,
        'bordered' => false,
        'hover' => true,
        'tableOptions' => ['class' => 'table-sm'],
        'columns' => [
          ['class' => 'yii\grid\SerialColumn'],

          // ===== Tanggal =================================================================
          [
            'attribute' => 'tanggal',
            'format' => 'raw',
            'contentOptions' => ['class' => 'text-center'],
            'headerOptions' => ['class' => 'text-center'],
            'value' => fn($m) => $m->tanggal ? Yii::$app->formatter->asDate($m->tanggal, 'php:d F Y') : '-',
          ],

          // ===== No Pengeluaran (Upload / Preview) ======================================
          [
            'attribute' => 'no_pengeluaran',
            'format' => 'raw',
            'value' => function ($m) {
              if ($m->status_pembayaran === 'Sudah Dibayar' && $m->bukti_pembayaran) {
                return Html::a(
                  Html::encode($m->no_pengeluaran),
                  '#',
                  [
                    'class' => 'view-proof-btn',
                    'data-id' => $m->id_pengeluaran,
                    'style' => 'text-decoration:underline;color:green;cursor:pointer',
                    'title' => 'Lihat bukti pembayaran'
                  ]
                );
              }
              return Html::a(
                Html::encode($m->no_pengeluaran),
                '#',
                [
                  'class' => 'upload-proof-btn',
                  'data-id' => $m->id_pengeluaran,
                  'data-pjax' => '0',
                  'style' => 'cursor:pointer;text-decoration:underline;',
                  'title' => 'Upload bukti pembayaran'
                ]
              );
            }
          ],
          // ===== Akun ===================================================================
          [
            'attribute' => 'accountkeluar_id',
            'label' => 'Akun Pengeluaran',
            'value' => fn($m) => $m->accountkeluar
              ? $m->accountkeluar->code . ' - ' . $m->accountkeluar->akun
              : '(Tidak ada)',
          ],
          'jenis_pembayaran',
          // ===== Vendor ================================================================
          [
            'attribute' => 'id_vendor',
            'value' => fn($m) => $m->vendor->nama_vendor ?? '(Tidak ada nama)'
          ],

          'keterangan',

          // ===== Jumlah ================================================================
          [
            'attribute' => 'jumlah',
            'format' => 'raw',
            'contentOptions' => ['class' => 'text-end'],
            'headerOptions' => ['class' => 'text-end'],
            'value' => fn($m) => 'Rp ' . number_format($m->jumlah, 0, ',', '.')
          ],

          // ===== Status Pembayaran ======================================================
          [
            'attribute' => 'status_pembayaran',
            'format' => 'raw',
            'contentOptions' => ['class' => 'text-center'],
            'headerOptions' => ['class' => 'text-center'],
            'value' => function ($m) {
              $status = $m->status_pembayaran;
              switch ($status) {
                case 'Sudah Dibayar':
                case 'Lunas':
                  $bg = '#28a7451A';
                  $color = '#28a745';
                  break;
                case 'Belum Dibayar':
                case 'Telat Bayar':
                default:
                  $bg = '#dc35451A';
                  $color = '#dc3545';
                  break;
              }
              return Html::tag('span', $status ?? 'Belum Dibayar', [
                'style' => "
                          display:inline-block;padding:6px 12px;border-radius:8px;
                          background-color:$bg;color:$color;font-weight:bold;font-size:12px;"
              ]);
            }
          ],

          // ===== Actions (view/edit/delete) ============================================
          [
            'header' => 'Actions',
            'format' => 'raw',
            'hAlign' => 'right',
            'value' => function ($m) {
              return
                Html::a('
        <svg class="icon-16" width="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M13.7476 20.4428H21.0002" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
          <path fill-rule="evenodd" clip-rule="evenodd" d="M12.78 3.79479C13.5557 2.86779 14.95 2.73186 15.8962 3.49173C15.9485 3.53296 17.6295 4.83879 17.6295 4.83879C18.669 5.46719 18.992 6.80311 18.3494 7.82259C18.3153 7.87718 8.81195 19.7645 8.81195 19.7645C8.49578 20.1589 8.01583 20.3918 7.50291 20.3973L3.86353 20.443L3.04353 16.9723C2.92866 16.4843 3.04353 15.9718 3.3597 15.5773L12.78 3.79479Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
          <path d="M11.021 6.00098L16.4732 10.1881" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
        </svg>
        ', '#', ['value' => Url::to(['update', 'id_pengeluaran' => $m->id_pengeluaran]), 'title' => 'Form Edit ' . Yii::t('app', 'Pemasukan'), 'class' => 'showModalButton text-success me-md-1']) .
                Html::a('
        <svg class="icon-16" width="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M19.3248 9.46826C19.3248 9.46826 18.7818 16.2033 18.4668 19.0403C18.3168 20.3953 17.4798 21.1893 16.1088 21.2143C13.4998 21.2613 10.8878 21.2643 8.27979 21.2093C6.96079 21.1823 6.13779 20.3783 5.99079 19.0473C5.67379 16.1853 5.13379 9.46826 5.13379 9.46826" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
          <path d="M20.708 6.23975H3.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
          <path d="M17.4406 6.239F73C16.6556 6.23973 15.9796 5.68473 15.8256 4.91573L15.5826 3.69973C15.4326 3.13873 14.9246 2.75073 14.3456 2.75073H10.1126C9.53358 2.75073 9.02558 3.13873 8.87558 3.69973L8.63258 4.91573C8.47858 5.68473 7.80258 6.23973 7.01758 6.23973" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
        </svg>
        ', '#', ['id_pengeluaran' => $m->id_pengeluaran, 'title' => 'Hapus Data?', 'class' => 'text-danger delete']);
            }
          ],
        ]
      ]); ?>
    </div>
  </div>
</div>

<?php Pjax::end(); ?>

<?php
$linkDelete = Url::to(['delete']);
$urlUploadProof = Url::to(['/vendorfinance/pengeluaran/upload-proof']);
$urlGetProof = Url::to(['/vendorfinance/pengeluaran/get-proof']);
$csrfToken = Yii::$app->request->getCsrfToken();

JSRegister::begin(['position' => \yii\web\View::POS_END]);
?>
<script>
  const urlUploadProof = "<?= $urlUploadProof ?>";
  const urlGetProof = "<?= $urlGetProof ?>";
  const csrfToken = "<?= $csrfToken ?>";

  let is_fetch = false;
  let timer = null;
  let lastQuery = '';

  // ========================== UPLOAD BUKTI =====================================
  $(document).on("click", ".upload-proof-btn", function() {
    const id = $(this).data("id");
    Swal.fire({
      title: "Upload Bukti Pembayaran",
      text: "Pilih file (gambar atau PDF).",
      input: "file",
      inputAttributes: {
        accept: "image/*,application/pdf"
      },
      showCancelButton: true,
      confirmButtonText: "Lanjut",
      cancelButtonText: "Batal",
      preConfirm: file => {
        if (!file) {
          Swal.showValidationMessage("Silakan pilih file terlebih dahulu");
          return false;
        }
        return new Promise((resolve, reject) => {
          const reader = new FileReader();
          reader.onload = () => {
            const preview = file.type.startsWith("image/") ?
              `<img src="${reader.result}" style="max-width:100%;border-radius:8px;">` :
              `<embed src="${reader.result}" type="${file.type}" width="100%" height="300">`;

            Swal.fire({
              title: "Konfirmasi Upload",
              html: `<p>Apakah file ini sudah benar?</p>${preview}`,
              showCancelButton: true,
              confirmButtonText: "Upload Sekarang",
              cancelButtonText: "Ganti File",
              reverseButtons: true
            }).then(r => {
              if (!r.isConfirmed) return reject();

              const fd = new FormData();
              fd.append("bukti", file);
              fd.append("_csrf", csrfToken);
              fetch(urlUploadProof + "?id_pengeluaran=" + id, {
                  method: "POST",
                  body: fd
                })
                .then(r => r.json())
                .then(d => {
                  if (d.status === "success") {
                    Swal.fire("Berhasil", d.message, "success");
                    $.pjax.reload({
                      container: "#gridDatapengeluaran"
                    });
                    resolve();
                  } else {
                    Swal.fire("Gagal", d.message, "error");
                    reject();
                  }
                })
                .catch(() => {
                  Swal.fire("Gagal", "Terjadi kesalahan koneksi", "error");
                  reject();
                });
            });
          };
          reader.readAsDataURL(file);
        });
      }
    });
  });

  // ========================== PREVIEW BUKTI ===================================
  $(document).on("click", ".view-proof-btn", function() {
    const id = $(this).data("id");
    $.get(urlGetProof, {
      id_pengeluaran: id
    }, function(res) {
      if (res.status === 'success') {
        const ext = res.ext.toLowerCase();
        const html = ext === 'pdf' ?
          `<embed src="${res.url}" type="application/pdf" width="100%" height="500">` :
          `<img src="${res.url}" style="max-width:100%;max-height:500px;">`;

        $("#proofModalContent").html(html);
        $("#proofModal").modal("show");
      } else {
        Swal.fire("Gagal", res.message, "error");
      }
    }).fail(() => Swal.fire("Gagal", "Tidak dapat mengambil bukti", "error"));
  });

  // ========================== DELETE DATA =====================================
  let isFetch = false;
  $(document).on("click", ".delete", function() {
    const id = $(this).attr("id_pengeluaran");
    Swal.fire({
      title: "Konfirmasi",
      text: "Apakah akan menghapus data?",
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Lanjutkan",
      cancelButtonText: "Batal"
    }).then(r => {
      if (!r.isConfirmed || isFetch) return;
      isFetch = true;

      $.post("<?= $linkDelete ?>", {
        id_pengeluaran: id,
        _csrf: csrfToken
      }, function(res) {
        if (res.status === 'success') {
          Swal.fire("OK", res.message, "success");
          $.pjax.reload({
            container: "#gridDatapengeluaran"
          });
        } else {
          Swal.fire("Gagal", res.message, "error");
        }
        isFetch = false;
      }, "json").fail(() => {
        Swal.fire("Gagal", "Koneksi error", "error");
        isFetch = false;
      });
    });
  });

  $(document).on('input', '#search-input', function() {
    lastQuery = $(this).val();
    clearTimeout(timer);
    if (lastQuery.length >= 3 || lastQuery.length === 0) {
      timer = setTimeout(() => {
        $.pjax.reload({
          container: '#gridDatapengeluaran',
          url: '<?= Url::to(['index']) ?>?PengeluaranSearch[queryString]=' + encodeURIComponent(lastQuery),
          replace: false,
          push: false,
          timeout: 1000,
        });
      }, 400);
    }
  });

  $(document).on('pjax:end', function() {
    $('#search-input').val(lastQuery);
    $('#search-input').prop('disabled', false);
  });
</script>
<?php JSRegister::end(); ?>