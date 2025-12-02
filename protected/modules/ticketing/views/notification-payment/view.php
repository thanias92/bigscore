<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\NotificationPayment */
/* @var $filter string */

// $this->title = 'Detail Notification';
$this->params['breadcrumbs'][] = ['label' => 'Notification', 'url' => ['index']];
// $this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>

<?php if ($type === 'payment') : ?>

  <!-- Bagian untuk Notification Payment -->
  <div class="notification-payment-view">
    <!-- <h3 class="mb-3">Detail Notification Payment</h3> -->

    <!-- Informasi Customer -->
    <div class="row mb-3">
      <div class="col-lg-12">
        <button type="button" class="btn btn-white border" style="border-radius: 8px;">
          <?= $model->pemasukan->deals->customer->customer_name ?? '-' ?>
        </button>
      </div>
    </div>

    <div class="row border py-2 mx-0 mt-3" style="border-radius: 8px;">
      <?php
      $customer = $model->pemasukan->deals->customer ?? null;
      $infoCustomer = [
        ['label' => 'Nama PIC', 'value' => $customer->pic_name ?? '-'],
        ['label' => 'Jabatan', 'value' => $customer->pic_workroles ?? '-'],
        ['label' => 'Email', 'value' => $customer->pic_email ?? '-'],
        ['label' => 'Telepon', 'value' => $customer->pic_phone ?? '-'],
      ];
      foreach ($infoCustomer as $row) : ?>
        <div class="col-12 d-flex mb-1">
          <div style="width: 100px; font-weight: reguler;"><?= $row['label'] ?></div>
          <div style="width: 10px;">:</div>
          <div><?= $row['value'] ?></div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Status Pembayaran -->
    <div class="p-3 mt-4" style="background-color: #FFFFFF; border: 1px solid #E1E3EA; border-radius: 8px;">
      <div class="row">
        <div class="col-lg-12 d-flex justify-content-between align-items-center">
          <div style="font-weight: semi bold; font-size: 14px;">Status Pembayaran :</div>
          <div class="dropdown">
            <button class="btn btn-light border" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="fas fa-ellipsis-v"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
              <li>
                <button class="dropdown-item d-flex align-items-center send-late-btn"
                  data-id="<?= $model->id_notification_payment ?>"
                  data-type="payment"
                  data-status="late1">
                  <span class="me-2 d-flex justify-content-center align-items-center"
                    style="width: 16px; height: 16px; border-radius: 50%; background-color: rgba(253, 171, 61, 0.15); border: 2px solid #FDAB3D;">
                    <i class="fas fa-exclamation" style="color: #FDAB3D; font-size: 10px;"></i>
                  </span>
                  Late 1
                </button>
              </li>
              <li>
                <button class="dropdown-item d-flex align-items-center send-late-btn"
                  data-id="<?= $model->id_notification_payment ?>"
                  data-type="payment"
                  data-status="late2">
                  <span class="me-2 d-flex justify-content-center align-items-center"
                    style="width: 16px; height: 16px; border-radius: 50%; background-color: rgba(255, 94, 3, 0.15); border: 2px solid #FF5E03;">
                    <i class="fas fa-exclamation" style="color: #FF5E03; font-size: 10px;"></i>
                  </span>
                  Late 2
                </button>
              </li>
              <li>
                <button class="dropdown-item d-flex align-items-center send-late-btn"
                  data-id="<?= $model->id_notification_payment ?>"
                  data-type="payment"
                  data-status="suspend">
                  <span class="me-2 d-flex justify-content-center align-items-center"
                    style="width: 16px; height: 16px; border-radius: 50%; background-color: rgba(201, 40, 30, 0.15); border: 2px solid #C9281E;">
                    <i class="fas fa-exclamation" style="color: #C9281E; font-size: 10px;"></i>
                  </span>
                  Suspend
                </button>
              </li>
            </ul>
          </div>
        </div>

        <div class="col-lg-12 mt-3">
          <div class="table-responsive">
            <table class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th>Jatuh Tempo</th>
                  <th>ke</th>
                  <th>Nominal</th>
                  <th>Nomor Faktur</th>
                  <th>Tanggal Bayar</th>
                  <th>Status</th>
                </tr>
              </thead>
              
              <tbody>
                <?php foreach ($cicilanData as $index => $row): ?>
                  <tr>
                    <td><?= Html::encode($row['jatuh_tempo']) ?></td>
                    <td><?= Html::encode($row['ke']) ?></td>
                    <td>Rp <?= isset($row['grand_total']) ? number_format((float)$row['grand_total'], 0, ',', '.') : '0' ?></td>
                    <td><?= Html::encode($row['no_faktur']) ?></td>
                    <td><?= Html::encode($row['tanggal_bayar']) ?></td>
                    <td><?= Html::encode($row['status']) ?></td>
                  </tr>
                <?php endforeach ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

  </div>

<?php elseif ($type === 'contract') : ?>

  <!-- Bagian untuk Notification Contract -->
  <div class="notification-contract-view">
    <!-- Informasi Customer -->
    <div class="row mb-3">
      <div class="col-lg-12">
        <button type="button" class="btn btn-white border" style="border-radius: 8px;">
          <?= $model->contract->invoice->deals->customer->customer_name ?? '-' ?>
        </button>
      </div>
    </div>

    <div class="row border py-2 mx-0 mt-3" style="border-radius: 8px;">
      <?php
      $customer = $model->contract->invoice->deals->customer ?? null;
      $infoCustomer = [
        ['label' => 'Nama PIC', 'value' => $customer->pic_name ?? '-'],
        ['label' => 'Jabatan', 'value' => $customer->pic_workroles ?? '-'],
        ['label' => 'Email', 'value' => $customer->pic_email ?? '-'],
        ['label' => 'Telepon', 'value' => $customer->pic_phone ?? '-'],
      ];
      foreach ($infoCustomer as $row) : ?>
        <div class="col-12 d-flex mb-1">
          <div style="width: 100px; font-weight: reguler;"><?= $row['label'] ?></div>
          <div style="width: 10px;">:</div>
          <div><?= $row['value'] ?></div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Status Contract -->
    <div class="p-3 mt-4" style="background-color: #FFFFFF; border: 1px solid #E1E3EA; border-radius: 8px;">
      <div class="row ">
        <div class="col-lg-12 d-flex justify-content-between align-items-center">
          <div style="font-weight: semi bold; font-size: 14px;">Status Contract :</div>
          <div class="dropdown">
            <button class="btn btn-light border" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="fas fa-ellipsis-v"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
              <li>
                <button class="dropdown-item d-flex align-items-center send-reminder-btn"
                  data-id="<?= $model->id_notification_contract ?>"
                  data-type="contract"
                  data-reminder="early">
                  <span class="me-2 d-flex justify-content-center align-items-center"
                    style="width: 16px; height: 16px; border-radius: 50%; background-color: rgba(253, 171, 61, 0.15); border: 2px solid #FDAB3D;">
                    <i class="fas fa-exclamation" style="color: #FDAB3D; font-size: 10px;"></i>
                  </span>
                  Early Reminder
                </button>
              </li>
              <li>
                <button class="dropdown-item d-flex align-items-center send-reminder-btn"
                  data-id="<?= $model->id_notification_contract ?>"
                  data-type="contract"
                  data-reminder="final">
                  <span class="me-2 d-flex justify-content-center align-items-center"
                    style="width: 16px; height: 16px; border-radius: 50%; background-color: rgba(201, 40, 30, 0.15); border: 2px solid #C9281E;">
                    <i class="fas fa-exclamation" style="color: #C9281E; font-size: 10px;"></i>
                  </span>
                  Final Reminder
                </button>
              </li>
            </ul>
          </div>
        </div>

        <div class="col-lg-12 mt-2">
          <div class="table-responsive">
            <table class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th>Start Date</th>
                  <th>End Date</th>
                  <th>Total Contract</th>
                  <th>Sisa Contract</th>
                  <th>Status Contract</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $contract = $model->contract ?? null;
                if ($contract) :
                  
                  $startDate = date('d/m/Y', strtotime($contract->start_date ?? ''));
                  $endDate = date('d/m/Y', strtotime($contract->end_date ?? ''));

                  // Hitung total dan sisa bulan kontrak
                  $start = !empty($contract->start_date) ? new \DateTime($contract->start_date) : null;
                  $end = !empty($contract->end_date) ? new \DateTime($contract->end_date) : null;
                  $now = new \DateTime('now');

                  function getFullMonthDiff(\DateTime $start, \DateTime $end): int
                  {
                    $diff = $start->diff($end);
                    return ($diff->y * 12) + $diff->m + ($diff->d >= 0 ? 0 : -1);
                  }

                  if ($start && $end) {
                    $totalMonth = getFullMonthDiff($start, $end);
                    $sisaMonth = ($now <= $end) ? getFullMonthDiff($now, $end) : 0;
                  } else {
                    $totalMonth = $sisaMonth = 0;
                  }

                  $statusContract = $contract->status_contract ?? 'now';
                  // Status notifikasi kontrak
                  $statusNotif = $model->status_contract_notification ?? 'Early Reminder';
                ?>
                  <tr>
                    <td><?= $startDate ?></td>
                    <td><?= $endDate ?></td>
                    <td><?= $totalMonth ?> Month</td>
                    <td><?= $sisaMonth ?> Month</td>
                    <td>
                      <span class="badge bg-warning text-dark"><?= $statusContract ?></span>
                    </td>
                    <td>
                      <span class="badge bg-warning text-dark"><?= $statusNotif ?></span>
                    </td>
                  </tr>
                <?php else : ?>
                  <tr>
                    <td colspan="6" class="text-center text-muted">Data kontrak tidak ditemukan.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

<?php endif; ?>

<?php
$sendLateUrl = \yii\helpers\Url::to(['notification-payment/send-late-notification']);
$sendContractReminderUrl = \yii\helpers\Url::to(['notification-payment/send-contract-reminder']);

$script = <<<JS
$('.send-late-btn').on('click', function(e) {
  e.preventDefault();

  if (!confirm("Kirim email pengingat ke customer?")) return;

  const id = $(this).data('id');
  const type = $(this).data('type');
  const status = $(this).data('status');

  $.ajax({
    url: '{$sendLateUrl}',
    type: 'POST',
    data: {
      id: id,
      type: type,
      status: status,
      _csrf: yii.getCsrfToken()
    },
    success: function(res) {
      if (res.status === 'success') {
        alert(res.message);
        location.reload(); // Reload halaman agar status terupdate
      } else {
        alert(res.message || 'Gagal mengirim email.');
      }
    },
    error: function() {
      alert('Terjadi kesalahan saat mengirim email.');
    }
  });
});

$('.send-reminder-btn').on('click', function(e) {
  e.preventDefault();

  if (!confirm("Kirim notifikasi kontrak ke customer?")) return;

  const id = $(this).data('id');
  const type = $(this).data('type');
  const reminder = $(this).data('reminder'); // <- diubah di sini

  $.ajax({
    url: '{$sendContractReminderUrl}',
    type: 'POST',
    data: {
      id: id,
      type: type,
      reminder: reminder, // <- ini juga
      _csrf: yii.getCsrfToken()
    },
    success: function(res) {
      if (res.reminder === 'success') {
        alert(res.message);
        location.reload(); // Reload halaman agar status terupdate
      } else {
        alert(res.message || 'Gagal mengirim email.');
      }
    },
    error: function() {
      alert('Terjadi kesalahan saat mengirim email.');
    }
  });
});


JS;

$this->registerJs($script);
?>