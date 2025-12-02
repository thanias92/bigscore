<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\grid\ActionColumn;
use yii\web\View;
use app\widgets\JSRegister;
use kartik\grid\GridView;
use yii\bootstrap5\Modal;
//kanza push ulang untuk hosting

$this->title = 'Implementation';
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile('https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css');
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
  select {
    width: 120px;
    border-radius: 10px;
    border: 1px solid #ccc;
  }

  table {
    border: 1px solid #C0BFC0;
  }

  table th a {
    color: #3F4254 !important;
    text-decoration: none;
  }

  table th a:hover {
    color: #333 !important;
  }

  /* Hilangkan semua border dulu */
  .table-custom,
  .table-custom th,
  .table-custom td {
    border: none !important;
  }

  /* Tambahkan hanya border horizontal (bottom untuk setiap baris) */
  .table-custom tr td,
  .table-custom tr th {
    border-bottom: 1px solid #ccc !important;
    /* kamu bisa ganti warna/ketebalan */
  }

  /* Opsional: tambahkan border-top hanya untuk header */
  .table-custom thead tr th {
    border-top: 1px solid #ccc !important;
    background-color: #E5E7EB;
    /* warna abu untuk header */
    color: #3F4254;
  }

  .kv-grid-table,
  .kv-grid-table th,
  .kv-grid-table td {
    border: none !important;
  }

  .kv-grid-table tr td,
  .kv-grid-table tr th {
    border-bottom: 1px solid #ccc !important;
  }

  .kv-grid-table tbody tr:last-child td {
    border-bottom: none !important;
  }

  body {
    background-color: rgba(245, 248, 250, 1) !important;
  }
</style>

<style>
  #jenis-filter {
    padding: 5px;
    width: 220px;
    border-radius: 5px;
    font-size: 0.9rem;
  }

  /* ====== CSS Baru untuk Search Bar Implementasi (disamakan dengan menu Task) ====== */

  /* Ini adalah container utama untuk input dan tombol search */
  #search_box {
    background-color: rgba(229, 231, 235, 1) !important;
    /* Latar belakang abu-abu terang */
    border: 1px solid rgba(192, 191, 192, 1) !important;
    /* Border abu-abu */
    border-radius: 5px !important;
    overflow: hidden;
    /* Penting untuk menjaga border-radius pada konten di dalamnya */
    display: flex;
    align-items: center;
    /* Menengahkan konten secara vertikal */
    height: 30px;
    /* Tinggi total kotak search */
    width: 220px;
    /* Lebar keseluruhan kotak search */
    padding: 0 !important;
    /* Reset padding inline yang mungkin ada */
  }

  /* Gaya untuk Input Field di dalam Search Box */
  #queryString {
    border: none !important;
    /* Hapus border default atau dari Tailwind */
    background-color: transparent !important;
    /* Pastikan transparan agar background dari #search_box terlihat */
    padding: 5px 8px !important;
    /* Padding yang sama dengan menu Task */
    flex-grow: 1;
    /* Biarkan mengisi ruang yang tersedia */
    font-family: 'Inter Regular', sans-serif;
    /* Font yang sama */
    font-size: 0.9rem !important;
    /* Ukuran font yang sama */
    color: #495057 !important;
    /* Warna teks yang diketik (default di menu task) */
    width: auto !important;
    /* Biarkan flex-grow yang mengatur lebar */
  }

  /* Gaya untuk Placeholder (teks "Search...") */
  #queryString::placeholder {
    color: #6c757d !important;
    /* Warna placeholder dari menu Task */
  }

  /* Gaya untuk container ikon/tombol search (garis pemisah dan tombol) */
  #search_box>div:last-child {
    border-left: 1px solid rgba(60, 60, 67, 0.3) !important;
    /* Warna dan ketebalan separator */
    height: 100%;
    /* Pastikan separator mengisi tinggi */
    display: flex;
    align-items: center;
    justify-content: center;
    padding-left: 1px;
    /* Padding ke kiri separator */
  }

  /* Gaya untuk Tombol Ikon (button) */
  #search_box button {
    background-color: transparent !important;
    border: none !important;
    padding: 5px 8px !important;
    /* Padding tombol dari menu Task */
    color: rgba(113, 128, 150, 1) !important;
    /* Warna teks/ikon default tombol */
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    font-size: 0.9rem !important;
    /* Penting untuk ukuran ikon jika menggunakan i */
  }

  /* Gaya untuk Ikon Search (img di dalam button) */
  #search_box button img {
    width: 15px !important;
    /* Ukuran ikon yang disamakan */
    height: 15px !important;
    /* Ukuran ikon yang disamakan */
    padding-top: 3px !important;
    /* Geser ikon ke bawah */
    filter: none !important;
    /* Hapus filter sebelumnya, biarkan warna asli ikon */
    color: rgba(72, 129, 173, 1) !important;
    /* Ini warna ikon yang sebenarnya dari menu task */
    /* Penting: Jika search.svg Anda hitam, Anda mungkin tetap perlu filter atau mengubah file SVG-nya */
    /* filter: brightness(0) invert(1); */
    /* Aktifkan jika ikon tetap hitam */
  }
</style>


<?php
$this->registerCss("#modal .modal-header { display: none; }");
Modal::begin([
  'id' => 'modal',
  'size' => 'modal-sm',
  'options' => [
    'data-bs-backdrop' => 'static',
    'data-bs-keyboard' => 'false',
    'tabindex' => false,
  ],
  'dialogOptions' => ['class' => 'modal-dialog-centered'],
]);

echo "<div id='modalContent'>sds</div>";

Modal::end();
?>

<div class="card">

  <div class="w-full flex flex-row justify-between p-4">
    <div class="flex flex-col gap-3 ">
      <div>Select Client</div>
      <div>
        <select name="nama_pelanggan" id="filter-nama-pelanggan" style="padding: 5px; width: 220px; border-radius: 5px; font-size: 0.9rem;
        " class="w-[140px] rounded-xl border-gray-300 border-1">
          <option value="">All</option>
          <?php foreach ($customers as $customer): ?>
            <option value="<?= htmlspecialchars($customer->customer_name) ?>">
              <?= htmlspecialchars($customer->customer_name) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="flex flex-row justify-end gap-4">
      <div>
        <select name="jenis_filter" id="jenis-filter" class="px-2 py-2 rounded-xl border-gray-300 border-1 w-54">
          <option value="">Filter</option>
          <?php foreach ($jenisFilter as $key => $customer): ?>
            <option value="<?= htmlspecialchars($key) ?>">
              <?= htmlspecialchars($customer) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <div id="search_box" style="width:220px;background:white;border:1px solid #E5E7EB;padding: 15px 1px 2px 8px;border-radius: 5px;font-size: 0.9rem;" class="flex flex-row justify-between items-center rounded-xl  px-2 py-1">
          <input type="text" name="queryString" id="queryString" value="<?= Yii::$app->request->get('queryString') ?>" class="border-1 border-black rounded-lg" placeholder="Search..." style="background:none;width:120px;">
          <div class="h-full flex flex-row items-center justify-center pl-1" style="border-left:1px solid #3C3C434D;">
            <button type="button" onClick="cari()" style="background:none;border:none;">
              <img src="/img/search.svg" alt="icon">
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="w-full p-4">
    <div id="gridview-data" class="border-1 rounded-lg" style="border: 1px solid #ccc;">
      <?=
      $this->render('_gridview_partial', [
        'dataProvider' => $dataProvider,
        'searchModel' => $searchModel,
      ]);
      ?>
    </div>
  </div>
</div>

<script>
  $('#filter-nama-pelanggan').on('change', function() {
    var selectedValue = $(this).val();

    $.ajax({
      url: '?customer_name=' + encodeURIComponent(selectedValue),
      type: 'GET',
      success: function(data) {
        $('#gridview-data').html(data);
      },
      error: function() {
        alert('Error loading data.');
      }
    });
  });


  function cari() {
    var jenisFilter = $('#jenis-filter').val();
    var query = $('#queryString').val();

    $.ajax({
      url: '?' + jenisFilter + '=' + encodeURIComponent(query),
      type: 'GET',
      success: function(data) {
        $('#gridview-data').html(data);
      },
      error: function() {
        alert('Error loading data.');
      }
    });
  }

  function alert() {
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
                  container: "#gridDataimplementation"
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
  }
</script>