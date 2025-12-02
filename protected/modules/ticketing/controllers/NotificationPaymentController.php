<?php

namespace app\modules\ticketing\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

use app\models\NotificationPayment;
use app\models\NotificationContract;
use app\models\Pemasukan;

use app\modules\ticketing\NotificationContractSearch;
use app\modules\ticketing\NotificationPaymentSearch;

/**
 * NotificationPaymentController implements the CRUD actions for NotificationPayment model.
 */
class NotificationPaymentController extends Controller
{
  /**
   * @inheritDoc
   */
  public function behaviors()
  {
    return array_merge(
      parent::behaviors(),
      [
        'verbs' => [
          'class' => VerbFilter::className(),
          'actions' => [
            'delete' => ['POST'],
          ],
        ],
      ]
    );
  }

  /**
   * Lists all NotificationPayment models.
   *
   * @return string
   */
  public function actionIndex($filter = 'notification_payment')
  {
    $paymentSearch = new NotificationPaymentSearch();
    $contractSearch = new NotificationContractSearch();

    if ($filter === 'notification_contract') {
      $dataProvider = $contractSearch->search(Yii::$app->request->queryParams);
    } else {
      $dataProvider = $paymentSearch->search(Yii::$app->request->queryParams);
    }

    return $this->render('index', [
      'searchModel' => $filter === 'notification_contract' ? $contractSearch : $paymentSearch,
      'dataProvider' => $dataProvider,
      'filter' => $filter
    ]);
  }

  /**
   * Displays a single NotificationPayment model.
   * @param int $id_notification Id Notification
   * @return string
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionView($type, $id)
  {
    $model = $this->findModel($type, $id);
    $cicilanData = [];

    $parentPemasukanId = null;

    // Ambil parent_pemasukan_id dari notification
    if ($type === 'payment') {
      $parentPemasukanId = $model->id_pemasukan ?? null; // <== ini adalah parent pemasukan
    } elseif ($type === 'contract') {
      $parentPemasukanId = $model->contract->pemasukan_id ?? null;
    }

    if ($parentPemasukanId) {
      // Ambil semua anak dari parent, jika ada
      $childIds = (new \yii\db\Query())
        ->select('pemasukan_id')
        ->from('pemasukan')
        ->where(['parent_id' => $parentPemasukanId])
        ->column();

      // Gabungkan parent + anak
      $allIds = array_merge([$parentPemasukanId], $childIds);

      // Ambil data cicilan berdasarkan semua pemasukan
      $cicilanData = (new \yii\db\Query())
        ->select([
          'pc.ke',
          'pc.jatuh_tempo',
          'pc.nominal',
          'pc.tanggal_bayar',
          'pc.status',
          'p.no_faktur'
        ])
        ->from('pemasukan_cicilan pc')
        ->innerJoin('pemasukan p', 'p.pemasukan_id = pc.pemasukan_id')
        ->where(['in', 'pc.pemasukan_id', $allIds])
        ->orderBy(['pc.jatuh_tempo' => SORT_ASC])
        ->all();
    }

    return $this->renderAjax('view', [
      'type' => $type,
      'model' => $model,
      'cicilanData' => $cicilanData,
    ]);
  }

  /**
   * Creates a new NotificationPayment model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   * @return string|\yii\web\Response
   */
  public function actionCreate()
  {
    $model = new NotificationPayment($type = 'payment');

    if ($this->request->isPost) {
      if ($type === 'payment') {
        $model = new NotificationPayment();
      } elseif ($type === 'contract') {
        $model = new NotificationContract();
      } else {
        throw new NotFoundHttpException('Tipe data tidak dikenali.');
      }

      if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($model->save()) {
          return ['status' => 'success', 'message' => 'Berhasil menambah data.'];
        } else {
          return ['status' => 'failed', 'message' => 'Gagal menambah data.', 'errors' => $model->errors];
        }
      }

      return $this->renderAjax('create', ['model' => $model, 'type' => $type]);
    }
  }
  /**
   * Updates an existing NotificationPayment model.
   * If update is successful, the browser will be redirected to the 'view' page.
   * @param int $id_notification Id Notification
   * @return string|\yii\web\Response
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionUpdate($type, $id)
  {
    $model = $this->findModel($type, $id);

    if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      if ($model->save()) {
        return ['status' => 'success', 'message' => 'Berhasil mengubah data.'];
      } else {
        return ['status' => 'failed', 'message' => 'Gagal mengubah data.', 'errors' => $model->errors];
      }
    }

    return $this->renderAjax('update', ['model' => $model, 'type' => $type]);
  }



  /**
   * Deletes an existing NotificationPayment model.
   * If deletion is successful, the browser will be redirected to the 'index' page.
   * @param int $id_notification Id Notification
   * @return \yii\web\Response
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionDelete($type, $id)
  {
    $model = $this->findModel($type, $id);
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    if ($model->delete()) {
      return [
        'status' => 'success',
        'message' => 'Berhasil Menghapus Data'
      ];
    } else {
      return [
        'status' => 'failed',
        'message' => 'Gagal Menghapus Data'
      ];
    }
  }

  public function actionGetDeals($deals_id)
  {
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

    $deals = \app\models\Deals::find()
      ->with(['customer', 'product'])
      ->where(['deals_id' => $deals_id])
      ->one();

    if ($deals) {
      return [
        'status' => 'success',
        'data' => [
          'deals_id' => $deals->deals_id,
          'customer_name' => $deals->customer->customer_name ?? null,
          'product_name' => $deals->product->product_name ?? null,
        ],
      ];
    } else {
      return ['status' => 'failed', 'message' => 'Data not found'];
    }
  }
  public function actionPemasukanParentOnly()
  {
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

    $data = \app\models\Pemasukan::find()
      ->alias('p')
      ->joinWith(['cicilan as c', 'c.notificationPayment as np'])
      ->where(['p.parent_id' => null])
      ->andWhere(['IS NOT', 'np.id_notification_payment', null])
      ->all();

    return [
      'status' => 'success',
      'data' => $data
    ];
  }

  public function actionGetPemasukan($pemasukan_id)
  {
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

    $pemasukan = \app\models\Pemasukan::find()
      ->where(['pemasukan_id' => $pemasukan_id])
      ->one();

    if ($pemasukan) {
      return [
        'status' => 'success',
        'data' => [
          'pemasukan_id' => $pemasukan->pemasukan_id,
          'status' => $pemasukan->status ?? null,
          'purchase_date' => $pemasukan->purchase_date ?? null,
          'total' => $pemasukan->total ?? null,
        ],
      ];
    } else {
      return ['status' => 'failed', 'message' => 'Data not found'];
    }
  }

  public function actionSendLateNotification()
  {
    try {
      Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
      $id = Yii::$app->request->post('id');
      $type = Yii::$app->request->post('type');
      $status = Yii::$app->request->post('status'); // late1, late2, suspend
  
      $model = $this->findModel($type, $id);
      $pemasukan = $model->pemasukan ?? null;
      $customer = $pemasukan->deals->customer ?? null;
  
      if (!$customer || !$customer->pic_email) {
        return ['status' => 'failed', 'message' => 'Email customer tidak tersedia.'];
      }
  
      $email = $customer->pic_email;
      $name = $customer->customer_name ?? '-';
  
      $allPemasukan = Pemasukan::find()
        ->joinWith(['deals'])
        ->where([
          'deals.customer_id' => $customer->customer_id,
          'pemasukan.status' => 'Belum Bayar',
        ])
        ->orderBy(['purchase_date' => SORT_ASC])
        ->all();
  
      $limit = $status === 'late2' ? 2 : ($status === 'suspend' ? 3 : 1);
  
      $selectedPemasukan = [];
      $lastMonth = null;
  
      foreach ($allPemasukan as $item) {
        if (!$item->purchase_date) continue;
  
        $currentMonth = (int)date('n', strtotime($item->purchase_date));
        $currentYear = (int)date('Y', strtotime($item->purchase_date));
  
        if (empty($selectedPemasukan)) {
          $selectedPemasukan[] = $item;
          $lastMonth = ['month' => $currentMonth, 'year' => $currentYear];
        } else {
          $expectedMonth = $lastMonth['month'] + 1;
          $expectedYear = $lastMonth['year'];
          if ($expectedMonth > 12) {
            $expectedMonth = 1;
            $expectedYear++;
          }
  
          if ($currentMonth === $expectedMonth && $currentYear === $expectedYear) {
            $selectedPemasukan[] = $item;
            $lastMonth = ['month' => $currentMonth, 'year' => $currentYear];
          } else {
            break;
          }
        }
  
        if (count($selectedPemasukan) >= $limit) break;
      }
  
      if (count($selectedPemasukan) < $limit) {
        return ['status' => 'failed', 'message' => 'Tidak ditemukan cicilan tertunggak secara berturut-turut.'];
      }
  
      $fakturContent = '';
      foreach ($selectedPemasukan as $item) {
        $faktur = $item->no_faktur ?? '-';
        $jatuhTempo = $item->purchase_date ? date('d/m/Y', strtotime($item->purchase_date)) : '-';
        $nominal = isset($item->grand_total) ? 'Rp ' . number_format($item->grand_total, 0, ',', '.') : '-';
  
        $fakturContent .= "<b>No. Faktur:</b> {$faktur}<br>";
        $fakturContent .= "<b>Tanggal Pembelian:</b> {$jatuhTempo}<br>";
        $fakturContent .= "<b>Jumlah:</b> {$nominal}<br><br>";
      }
  
      $emailBody = "
          Yth {$name},<br><br>
          Kami ingin menginformasikan bahwa pembayaran untuk tagihan berikut telah melewati batas waktu jatuh tempo:<br><br>
          {$fakturContent}
          Mohon segera dilakukan pembayaran untuk menghindari gangguan layanan.<br><br>
          Terima kasih atas perhatian Anda.<br><br>
          Hormat kami,<br>
          PT Bigs Integrasi Teknologi
      ";
  
      $subject = 'Pemberitahuan Keterlambatan Pembayaran Tagihan';
      if ($status === 'late1') {
        $model->status_payment_notification = 'Late (1)';
      } elseif ($status === 'late2') {
        $model->status_payment_notification = 'Late (2)';
      } elseif ($status === 'suspend') {
        $model->status_payment_notification = 'Suspend';
      } else {
        return ['status' => 'failed', 'message' => 'Status pengingat tidak valid.'];
      }
  
      $success = Yii::$app->mailer->compose()
        ->setFrom(['info@bigsgroup.co.id' => 'PT Bigs Integrasi Teknologi'])
        ->setTo($email)
        ->setSubject($subject)
        ->setHtmlBody($emailBody)
        ->send();
  
      if ($success) {
        if ($model->save(false)) {
          return ['status' => 'success', 'message' => "Email {$status} berhasil dikirim dan status disimpan."];
        } else {
          return ['status' => 'failed', 'message' => 'Email terkirim, tapi gagal menyimpan status.'];
        }
      }
  
      return ['status' => 'failed', 'message' => 'Gagal mengirim email.'];
    } catch (\Throwable $e) {
      Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
      return [
        'status' => 'failed',
        'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
      ];
    }
  }
  
  public function actionSendContractReminder()
  {
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

    $id = Yii::$app->request->post('id');
    $type = Yii::$app->request->post('type');
    $reminder = Yii::$app->request->post('reminder'); // 'early' atau 'final'

    $model = $this->findModel($type, $id);
    $contract = $model->contract ?? null;
    $invoice = $contract->invoice ?? null;
    $customer = $invoice->deals->customer ?? null;

    if (!$customer || !$customer->pic_email || !$contract) {
      return ['status' => 'failed', 'message' => 'Data kontrak atau email customer tidak tersedia.'];
    }
    $email = $customer->pic_email;
    $name = $customer->customer_name ?? '-';

    $today = new \DateTime();
    $endDate = new \DateTime($contract->end_date ?? 'now');
    $interval = $today->diff($endDate)->days;
    $isPast = $today > $endDate;

    if ($reminder === 'early' && $interval > 31) {
      return ['status' => 'failed', 'message' => 'Belum waktunya kirim Early Reminder.'];
    }

    if ($reminder === 'final' && !$isPast && $interval > 0) {
      return ['status' => 'failed', 'message' => 'Masa kontrak belum berakhir.'];
    }

    $subject = $reminder === 'early'
      ? 'Pemberitahuan Perpanjangan Kontrak'
      : 'Kontrak Berakhir - Layanan Akan Dinonaktifkan';

    $emailBody = $reminder === 'early'
      ? "Yth {$name},<br><br>
        Semoga Anda dalam keadaan baik.<br><br>

        Terima kasih atas kepercayaan Anda dalam menggunakan aplikasi Emesys untuk mendukung operasional Anda.<br><br>

        Kami ingin menginformasikan bahwa masa kontrak penggunaan aplikasi Anda akan berakhir pada <b>" . $endDate->format('d/m/Y') . "</b>.<br>
        Dengan demikian, masih tersisa waktu 1 bulan hingga kontrak berakhir.<br><br>

        Untuk memastikan layanan tetap berjalan tanpa gangguan, Anda dapat memperpanjang kontrak sebelum tanggal tersebut.<br>
        Kami menawarkan beberapa pilihan paket perpanjangan yang dapat disesuaikan dengan kebutuhan Anda.<br><br>

        Jika Anda membutuhkan informasi lebih lanjut atau ingin melakukan perpanjangan kontrak, silakan hubungi kami melalui:<br>
        Email: <b>bigsintegrasi@gmail.com</b><br>
        Telepon/WhatsApp: <b>081311112222</b><br><br>

        Kami akan dengan senang hati membantu Anda.<br><br>

        Terima kasih atas perhatian Anda, dan kami berharap dapat terus melayani Anda dengan layanan terbaik.<br><br>

        Hormat kami,<br>
        PT Bigs Integrasi Teknologi"

      : "Yth {$name},<br><br>
        Semoga Anda dalam keadaan baik.<br><br>

        Kami ingin menginformasikan bahwa masa kontrak penggunaan aplikasi Emesys Anda telah berakhir pada <b>" . $endDate->format('d/m/Y') . "</b>, yaitu <b>besok</b>.<br><br>

        Jika kontrak tidak diperpanjang, akses ke aplikasi akan dinonaktifkan mulai <b>" . $endDate->modify('+1 day')->format('d/m/Y') . "</b> untuk memastikan kesesuaian dengan kebijakan kami terkait perjanjian layanan.<br><br>

        Untuk menghindari gangguan layanan, kami mendorong Anda untuk segera melakukan perpanjangan kontrak.<br><br>

        Silakan hubungi kami untuk mendiskusikan perpanjangan kontrak, kami menyediakan pilihan perpanjangan secara praktis melalui email atau WhatsApp:<br>
        Email: <b>bigsintegrasi@gmail.com</b><br>
        Telepon/WhatsApp: <b>081311112222</b><br><br>

        Kami berkomitmen untuk mendukung kesuksesan usaha Anda melalui layanan kami.<br>
        Kami berharap dapat terus memenuhi kebutuhan Anda di masa mendatang.<br><br>

        Hormat kami,<br>
        PT Bigs Integrasi Teknologi";


    // Update status notifikasi
    $model->status_contract_notification = $reminder === 'early' ? 'Early Reminder' : 'Final Reminder';

    // Kirim email
    $success = Yii::$app->mailer
      ->compose()
      ->setFrom(['info@bigsgroup.co.id' => 'PT Bigs Integrasi Teknologi'])
      ->setTo($email)
      ->setSubject($subject)
      ->setHtmlBody($emailBody)
      ->send();

    if ($success && $model->save(false)) {
      return ['status' => 'success', 'message' => 'Email notifikasi berhasil dikirim.'];
    }

    return ['status' => 'failed', 'message' => 'Gagal mengirim email.'];
  }



  /**
   * Finds the NotificationPayment model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param int $id_notification Id Notification
   * @return NotificationPayment the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($type, $id)
  {
    if ($type === 'payment') {
      return \app\models\NotificationPayment::findOne(['id_notification_payment' => $id]);
    } elseif ($type === 'contract') {
      return \app\models\NotificationContract::findOne(['id_notification_contract' => $id]);
    }

    throw new NotFoundHttpException('Tipe data tidak dikenali.');
  }
}
