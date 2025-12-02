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

    .table-custom,
    .table-custom th,
    .table-custom td {
        border: none !important;
    }

    .table-custom tr td,
    .table-custom tr th {
        border-bottom: 1px solid #ccc !important;
    }

    .table-custom thead tr th {
        border-top: 1px solid #ccc !important;
        background-color: #E5E7EB;
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

    .tabel-head {
        background-color: rgb(229, 231, 235) !important;
        border: none !important;
        color: rgb(63, 66, 84) !important;
        text-align: center !important;
    }

    .bg-button {
        background: #27465E;
        color: white;
    }

    .bg-button:hover {
        background: #0e314c;
    }

    body {
        background-color: rgba(245, 248, 250, 1) !important;
    }
</style>

<?php yii\bootstrap5\Modal::begin([
    'headerOptions' => ['id' => 'modalHeader'],
    'id' => 'modal',
    'size' => 'modal-lg',
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
    <!-- <div class="ml-4 mt-4">
        <a href="/task/implementation/index" class="inline-flex items-center text-blue-600 hover:underline">
            &#8592;
            <span class="ml-2">Kembali</span>
        </a>
    </div> -->
    <div class="ml-4 mt-4">
        <a href="/task/implementation/index" class="inline-flex items-center px-4 py-2 bg-blue-900 text-white rounded hover:bg-blue-700 transition">
            &#8592;
            <span class="ml-2">Kembali</span>
        </a>
    </div>

    <div class="w-full flex flex-row justify-between p-4">
        <div class="flex flex-col gap-3 ">
            <div>Select Client</div>
            <div>
                <select name="nama_pelanggan" id="filter-nama-pelanggan" style="padding: 5px; width: 220px; border-radius: 5px; font-size: 0.9rem;" class="w-[140px] px-2 py-2 rounded-xl border-gray-300 border-1">
                    <?php foreach ($customers as $customer): ?>
                        <option value="<?= htmlspecialchars($customer['deals_id']) ?>" <?= @$customer['deals_id'] == $deal ? 'selected' : '' ?>>
                            <?= htmlspecialchars($customer['customer_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div>
            <h2> <?= $customersDeal->customer_name ?></h2>
        </div>
        <div class="flex flex-row justify-end gap-4">

            <!-- <div>
                <div style="background:#E5E7EB;border:1px solid #E5E7EB;" class="flex flex-row justify-between items-center rounded-xl  px-2 py-1">
                    <input type="text" name="queryString" id="queryString" value="<?= Yii::$app->request->get('queryString') ?>" class="border-1 border-black rounded-lg" placeholder="Search..." style="background:none;width:90px;">
                    <div class="h-full flex flex-row items-center justify-center pl-1" style="border-left:1px solid #3C3C434D;">
                        <button type="button" onClick="cari()" style="background:none;border:none;">
                            <img src="/img/search.svg" alt="icon">
                        </button>
                    </div>
                </div>
            </div> -->
            <div>
                <?= Html::button('+Add Aktivitas', ['value' => Url::to(['create', 'deals_id' => $deal]), 'title' => 'Form ' . 'Implementation', 'class' => 'showModalButton flex flex-row justify-center items-center rounded-lg bg-button px-4 py-2 text-sm']); ?>
            </div>
        </div>
    </div>

    <?php
    $total = count($dataProvider);
    $done = 0;

    foreach ($dataProvider as $item) {
        if (strtolower(trim($item['status'])) === 'done') {
            $done++;
        }
    }

    $progress = $total > 0 ? round(($done / $total) * 100) : 0;
    ?>

    <div class="px-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Progress Implementasi: <?= $progress ?>%
        </label>
        <div class="w-full bg-gray-200 rounded-full h-4">
            <div class="bg-green-500 h-4 rounded-full transition-all duration-300 ease-in-out" style="width: <?= $progress ?>%;"></div>
        </div>
    </div>

    <div class="w-full p-4">

        <div id="gridview-data" class="border-1 rounded-lg" style="border: 1px solid #ccc;">
            <div id="w0" class="grid-view is-bs4 kv-grid-bs4 hide-resize" data-krajee-grid="kvGridInit_fab0ef07" data-krajee-ps="ps_w0_container">
                <div id="w0-container" class="table-responsive kv-grid-container">
                    <table class="table kv-grid-table kv-table-wrap" style="border-collapse: collapse;">
                        <thead class="kv-table-header w0">
                            <tr>
                                <th class="tabel-head">No</th>
                                <th class="tabel-head">
                                    Aktivitas
                                </th>
                                <th class="tabel-head">
                                    Detail
                                </th>
                                <th class="tabel-head">
                                    Mulai
                                </th>
                                <th class="tabel-head">
                                    Selesai
                                </th>
                                <th class="tabel-head">
                                    PIC
                                </th>
                                <th class="tabel-head">
                                    Catatan
                                </th>
                                <th class="tabel-head">
                                    Status
                                </th>
                                <th class="tabel-head">
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($dataProvider as $value) {
                            ?>
                                <tr class="w0" style="border: none;color:black;" data-key="0">
                                    <td>
                                        <input type="checkbox" class="status-checkbox" data-id="<?= $value['id_implementasi'] ?>" <?= $value['status'] === 'Done' ? 'checked' : '' ?> <?= $value['status'] === 'Done' ? 'disabled' : '' ?>>
                                    </td>
                                    <td>
                                        <strong><?php print($value['activity_title']); ?></strong>
                                    </td>
                                    <td>

                                        <!-- <button type="button" class="showModalButton btn btn-link p-0 text-decoration-underline" value="/task/implementation/alert?deals_id=5" title="">THT Ahmad yani</button> -->
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>

                                    </td>
                                    <td align="center">
                                        <?php
                                        if ($value['status'] === 'Done') {
                                            echo '<span style="background-color:#D1FAE5;color:#065F46;padding:4px 8px;border-radius:6px;">Done</span>';
                                        } elseif ($value['status'] === 'Open') {
                                            echo '<span style="background-color:#FEE2E2;color:#991B1B;padding:4px 8px;border-radius:6px;">Open</span>';
                                        } elseif ($value['status'] === 'In Progress') {
                                            echo '<span style="background-color:#DBEAFE;color:#1E40AF;padding:4px 8px;border-radius:6px;">In Progress</span>';
                                        } else {
                                            echo '<span style="background-color:#FEE2E2;color:#1E40AF;padding:4px 8px;border-radius:6px;">Open</span>';
                                        }
                                        ?>
                                    </td>
                                    <td width="10%">
                                        <div class="flex flex-row justify-center items-center gap-4">
                                            <?=
                                            Html::a(
                                                '
                                                    <svg class="icon-16" width="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M13.7476 20.4428H21.0002" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12.78 3.79479C13.5557 2.86779 14.95 2.73186 15.8962 3.49173C15.9485 3.53296 17.6295 4.83879 17.6295 4.83879C18.669 5.46719 18.992 6.80311 18.3494 7.82259C18.3153 7.87718 8.81195 19.7645 8.81195 19.7645C8.49578 20.1589 8.01583 20.3918 7.50291 20.3973L3.86353 20.443L3.04353 16.9723C2.92866 16.4843 3.04353 15.9718 3.3597 15.5773L12.78 3.79479Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                    <path d="M11.021 6.00098L16.4732 10.1881" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                    </svg>
                                                    ',
                                                '#',
                                                [
                                                    'value' => Url::to(['update', 'deals_id' => $deal, 'id_implementasi' => $value['id_implementasi']],),
                                                    'title' => 'Form Edit ' . 'Implementation',
                                                    'class' => 'showModalButton text-success me-md-1'
                                                ]
                                            ) .

                                                Html::a('
                                                    <svg class="icon-16" width="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M19.3248 9.46826C19.3248 9.46826 18.7818 16.2033 18.4668 19.0403C18.3168 20.3953 17.4798 21.1893 16.1088 21.2143C13.4998 21.2613 10.8878 21.2643 8.27979 21.2093C6.96079 21.1823 6.13779 20.3783 5.99079 19.0473C5.67379 16.1853 5.13379 9.46826 5.13379 9.46826" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                    <path d="M20.708 6.23975H3.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                    <path d="M17.4406 6.23973C16.6556 6.23973 15.9796 5.68473 15.8256 4.91573L15.5826 3.69973C15.4326 3.13873 14.9246 2.75073 14.3456 2.75073H10.1126C9.53358 2.75073 9.02558 3.13873 8.87558 3.69973L8.63258 4.91573C8.47858 5.68473 7.80258 6.23973 7.01758 6.23973" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                    </svg>
                                                    ', '#', ['data-id' => $value['id_implementasi'], 'title' => 'Hapus Data?', 'class' => 'text-danger delete delete-implementasi'])
                                            ?>
                                        </div>
                                    </td>
                                </tr>

                                <?php
                                foreach ($value['detail_implementasi'] as $valDetail) {
                                ?>
                                    <tr class="w0" style="border: none;color:black;" data-key="0">
                                        <td>
                                            <input type="checkbox" class="status-checkbox-detail" data-id="<?= $valDetail['id_implementasi_detail'] ?>" <?= $valDetail['status'] === 'Done' ? 'checked' : '' ?> <?= $valDetail['status'] === 'Done' ? 'disabled' : '' ?>>
                                        </td>
                                        <td>
                                            <?php print($valDetail['activity']); ?>
                                        </td>
                                        <td>
                                            <a class="btn btn-link p-0 text-decoration-underline" target="_blank" href="<?php print($valDetail['detail']); ?>" title="<?php print($valDetail['detail']); ?>"><?php print($valDetail['detail']); ?></a>
                                        </td>
                                        <td>
                                            <?php print($valDetail['start_date']); ?>
                                        </td>
                                        <td>
                                            <?php print($valDetail['completion_date']); ?>
                                        </td>
                                        <td><?php print($valDetail['pic_aktivitas']); ?></td>
                                        <td>
                                            <?php print($valDetail['notes']); ?>
                                        </td>
                                        <td align="center">
                                            <?php
                                            if (@$valDetail['status'] === 'Done') {
                                                echo '<span style="background-color:#D1FAE5;color:#065F46;padding:4px 8px;border-radius:6px;">Done</span>';
                                            } elseif (@$valDetail['status'] === 'Open') {
                                                echo '<span style="background-color:#FEE2E2;color:#991B1B;padding:4px 8px;border-radius:6px;">Open</span>';
                                            } elseif (@$valDetail['status'] === 'In Progress') {
                                                echo '<span style="background-color:#DBEAFE;color:#1E40AF;padding:4px 8px;border-radius:6px;">In Progress</span>';
                                            } else {
                                                echo '<span style="background-color:#FEE2E2;color:#991B1B;padding:4px 8px;border-radius:6px;">Open</span>';
                                            }
                                            ?>
                                        </td>
                                        <td width="10%">
                                            <div class="flex flex-row justify-center items-center gap-4">
                                                <?=
                                                Html::a('
                                                        <svg class="icon-16" width="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M13.7476 20.4428H21.0002" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M12.78 3.79479C13.5557 2.86779 14.95 2.73186 15.8962 3.49173C15.9485 3.53296 17.6295 4.83879 17.6295 4.83879C18.669 5.46719 18.992 6.80311 18.3494 7.82259C18.3153 7.87718 8.81195 19.7645 8.81195 19.7645C8.49578 20.1589 8.01583 20.3918 7.50291 20.3973L3.86353 20.443L3.04353 16.9723C2.92866 16.4843 3.04353 15.9718 3.3597 15.5773L12.78 3.79479Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                        <path d="M11.021 6.00098L16.4732 10.1881" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                        </svg>
                                                        ', '#', ['value' => Url::to(['updatedetail', 'id_implementasi_detail' => $valDetail['id_implementasi_detail']]), 'title' => 'Form Edit Detail '  . 'Implementation', 'class' => 'showModalButton text-success me-md-1']) .
                                                    Html::a('
                                                        <svg class="icon-16" width="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M19.3248 9.46826C19.3248 9.46826 18.7818 16.2033 18.4668 19.0403C18.3168 20.3953 17.4798 21.1893 16.1088 21.2143C13.4998 21.2613 10.8878 21.2643 8.27979 21.2093C6.96079 21.1823 6.13779 20.3783 5.99079 19.0473C5.67379 16.1853 5.13379 9.46826 5.13379 9.46826" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                        <path d="M20.708 6.23975H3.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                        <path d="M17.4406 6.23973C16.6556 6.23973 15.9796 5.68473 15.8256 4.91573L15.5826 3.69973C15.4326 3.13873 14.9246 2.75073 14.3456 2.75073H10.1126C9.53358 2.75073 9.02558 3.13873 8.87558 3.69973L8.63258 4.91573C8.47858 5.68473 7.80258 6.23973 7.01758 6.23973" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                        </svg>
                                                        ', '#', ['data-id' => $valDetail['id_implementasi_detail'], 'title' => 'Hapus Data?', 'class' => 'text-danger delete delete-detail'])
                                                ?>
                                            </div>
                                        </td>
                                    </tr>
                            <?php
                                }
                            }
                            ?>


                        </tbody>
                    </table>
                </div>
            </div>
        </div>



    </div>
</div>

<script>
    $('#filter-nama-pelanggan').on('change', function() {
        var idDeals = $(this).val();
        var url = 'proses';
        if (idDeals) {
            url += '?deals_id=' + encodeURIComponent(idDeals);
        }
        window.location.href = url;
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

    function konfirmasiHapus(id, urlDelete, pjaxContainer = null) {
        Swal.fire({
            title: 'Yakin ingin menghapus data ini?',
            text: 'Data yang dihapus tidak dapat dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: urlDelete,
                    type: 'POST',
                    data: {
                        id: id
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire('Berhasil!', response.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Gagal!', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error!', 'Terjadi kesalahan saat menghapus.', 'error');
                    }
                });
            }
        });
    }


    $(document).on('click', '.delete-implementasi', function(e) {
        e.preventDefault();

        const id = $(this).data('id');
        const url = '/task/implementation/delete?id=' + id;
        const pjaxContainer = '#gridDataimplementation';

        konfirmasiHapus(id, url, pjaxContainer);
    });

    $(document).on('click', '.delete-detail', function(e) {
        e.preventDefault();

        const id = $(this).data('id');
        const url = '/task/implementation/deletedetail?id=' + id;
        const pjaxContainer = '#gridDataimplementation';

        konfirmasiHapus(id, url, pjaxContainer);
    });

    $('.status-checkbox').on('change', function() {
        const checkbox = $(this);
        const id = checkbox.data('id');
        const isChecked = checkbox.is(':checked');
        const newStatus = isChecked ? 'Done' : 'Open';

        $.ajax({
            url: '/task/implementation/update-status',
            type: 'POST',
            data: {
                id_implementasi: id,
                status: newStatus
            },
            success: function(response) {
                if (response.success) {

                    if (isChecked) {
                        checkbox.closest('tr').find('td:nth-child(8)').html(
                            '<span style="background-color:#D1FAE5;color:#065F46;padding:4px 8px;border-radius:6px;">Done</span>'
                        );
                    } else {
                        checkbox.closest('tr').find('td:nth-child(8)').html(
                            '<span style="background-color:#FEE2E2;color:#991B1B;padding:4px 8px;border-radius:6px;">Open</span>'
                        );
                    }

                    Swal.fire('Status Diperbaharui', response.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Status gagal diperbarui.'
                    });
                    checkbox.prop('checked', !isChecked);
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Terjadi kesalahan saat mengirim data.'
                });
                checkbox.prop('checked', !isChecked);
            }
        });
    });

    $('.status-checkbox-detail').on('change', function() {
        const checkbox = $(this);
        const id = checkbox.data('id');
        const isChecked = checkbox.is(':checked');
        const newStatus = isChecked ? 'Done' : 'Open';

        $.ajax({
            url: '/task/implementation/update-status-detail',
            type: 'POST',
            data: {
                id_implementasi: id,
                status: newStatus
            },
            success: function(response) {
                if (response.success) {

                    if (isChecked) {
                        checkbox.closest('tr').find('td:nth-child(8)').html(
                            '<span style="background-color:#D1FAE5;color:#065F46;padding:4px 8px;border-radius:6px;">Done</span>'
                        );
                    } else {
                        checkbox.closest('tr').find('td:nth-child(8)').html(
                            '<span style="background-color:#FEE2E2;color:#991B1B;padding:4px 8px;border-radius:6px;">Open</span>'
                        );
                    }

                    Swal.fire('Status Diperbaharui', response.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Status gagal diperbarui.'
                    });
                    checkbox.prop('checked', !isChecked);
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Terjadi kesalahan saat mengirim data.'
                });
                checkbox.prop('checked', !isChecked);
            }
        });
    });
</script>