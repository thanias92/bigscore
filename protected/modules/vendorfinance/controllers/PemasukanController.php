<?php

namespace app\modules\vendorfinance\controllers;

use Yii;
use yii\web\Controller;
use app\models\Accountkeluar;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\helpers\Url;
use yii\web\Response;
use app\models\Pemasukan;
use app\models\Pengaturanakun;
use app\models\PenerimaanPembayaran;
use app\models\PemasukanCicilan;
use app\modules\vendorfinance\PemasukanSearch;
use DateTime;
use Mpdf\Mpdf;
use yii\helpers\ArrayHelper; // Ditambahkan untuk ArrayHelper::map

class PemasukanController extends Controller
{
  public function behaviors()
  {
    return array_merge(parent::behaviors(), [
      'verbs' => [
        'class' => VerbFilter::className(),
        'actions' => [
          'delete' => ['POST'],
        ],
      ],
    ]);
  }

  public function actionIndex()
  {
    $searchModel = new PemasukanSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    $setting = Pengaturanakun::findOne(1);

    // Filter hanya data cicilan yang memiliki parent_id
    $dataProvider->query->andWhere(['IS NOT', 'pemasukan.no_faktur', null]);

    foreach ($dataProvider->models as $model) {
      $model->updateStatusIfLate();
    }

    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
      'setting' => $setting,
    ]);
  }

  public function actionView($pemasukan_id)
  {
    // Re-route to details view if it's an installment invoice
    $model = $this->findModel($pemasukan_id);
    if ($model->purchase_type === 'Outright Purchase - Installments' && $model->cicilan > 0) {
      return $this->redirect(['view-pemasukan-details', 'pemasukan_id' => $pemasukan_id]);
    }
    return $this->renderAjax('view', [
      'model' => $model,
    ]);
  }

  public function actionCreate($deals_id = null)
  {
    $model = new Pemasukan();

    // Handle AJAX preload Deals
    if (Yii::$app->request->isAjax && $deals_id !== null && Yii::$app->request->isGet) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      $deals = \app\models\Deals::findOne($deals_id);

      if (!$deals || !$deals->customer) {
        return ['status' => 'error', 'message' => 'Data tidak ditemukan'];
      }

      return [
        'status' => 'success',
        'customer_name' => $deals->customer->customer_name,
        'customer_email' => $deals->customer->customer_email,
        'produk' => $deals->produk,
        'unit' => $deals->unit_product ?? 1,
        'price_product' => $deals->price_product,
        'total' => $deals->total,
        'purchase_type' => $deals->purchase_type,
        'description' => $deals->description,
      ];
    }

    $akunPemasukanList = Accountkeluar::find()
      ->where(['ilike', 'penggunaan', 'pemasukan'])
      ->andWhere([
        'or',
        ['like', 'code', '4-%', false],
        ['like', 'code', '7-%', false],
      ])
      ->orderBy(['akun' => SORT_ASC])
      ->all();

    if (Yii::$app->request->isPost) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      $post = Yii::$app->request->post();

      if ($model->load($post)) {
        foreach (['purchase_date', 'tgl_jatuhtempo'] as $attr) {
          if ($model->$attr)
            $model->$attr = DateTime::createFromFormat('d/m/Y', $model->$attr)->format('Y-m-d');
        }

        if ($model->deals_id) {
          $deals = \app\models\Deals::find()->with('product')->where(['deals_id' => $model->deals_id])->one();
          if ($deals && $deals->product) {
            $model->unit = $deals->unit_product;
          }
        }

        $sub_total = (float)($model->sub_total ?? 0);
        $diskon_persen = (float)($model->diskon ?? 0);
        $ppn = $sub_total * 0.11;
        $diskon = $sub_total * $diskon_persen / 100;
        $grand_total = $sub_total + $ppn - $diskon;

        $model->grand_total = $grand_total;
        $model->sisa_tagihan = $grand_total;
        $model->status = 'Menunggu Pembayaran';

        $transaction = Yii::$app->db->beginTransaction();
        try {
          if ($model->save(false)) {
            if ($deals && $deals->purchase_type === 'Outright Purchase - Installments' && $model->cicilan > 1) {
              $jumlahCicilan = (int)$model->cicilan;
              $baseDate = new \DateTime($model->purchase_date);
              $cicilanAmount = round($grand_total / $jumlahCicilan, 2);

              // Simpan parent tanpa cicilan
              $model->save(false); // parent
              $parentId = $model->pemasukan_id;
              $interval = floor(12 / $jumlahCicilan); // jarak antar cicilan dalam bulan
              $jatuhTempo = new \DateTime($model->tgl_jatuhtempo); // Gunakan tanggal jatuh tempo pertama

              for ($i = 0; $i < $jumlahCicilan; $i++) {
                if ($i > 0) {
                  $jatuhTempo->modify("+$interval months");
                }

                $prefix = 'INV/' . $jatuhTempo->format('Y') . '/' . $jatuhTempo->format('m') . '/';

                $lastFaktur = Pemasukan::find()
                  ->where(['like', 'no_faktur', $prefix . '%', false])
                  ->andWhere(['deleted_at' => null])
                  ->orderBy(['pemasukan_id' => SORT_DESC])
                  ->one();

                $nextNum = ($lastFaktur && preg_match('/\/(\d{3})$/', $lastFaktur->no_faktur, $m))
                  ? ((int)$m[1] + 1)
                  : 1;

                $noFaktur = $prefix . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

                // Simpan child pemasukan
                $child = new Pemasukan();
                $child->attributes = $model->attributes;
                $child->isNewRecord = true;
                $child->parent_id = $parentId;
                $child->purchase_date = $jatuhTempo->format('Y-m-d');
                $child->tgl_jatuhtempo = $jatuhTempo->format('Y-m-d');
                $child->grand_total = $cicilanAmount;
                $child->sisa_tagihan = $cicilanAmount;
                $child->no_faktur = $noFaktur;
                $child->cicilan = $jumlahCicilan; // tetap 6 di setiap child
                $child->save(false);

                $cicilan = new PemasukanCicilan();
                $cicilan->pemasukan_id = $child->pemasukan_id;
                $cicilan->ke = $i + 1;
                $cicilan->nominal = $cicilanAmount;
                $cicilan->jatuh_tempo = $jatuhTempo->format('Y-m-d');
                $cicilan->status = 'Menunggu';
                $cicilan->save(false);
              }

              $transaction->commit();
              return ['status' => 'success', 'message' => "$jumlahCicilan cicilan berhasil dibuat"];
            }

            if ($deals && $deals->purchase_type === 'Outright Purchase - Installments' && $model->cicilan == 1) {
              $this->createInstallmentRecords($model);
            }

            // ✅ Tangani pembayaran langsung (tanpa cicilan)
            if ((int)$model->cicilan <= 1 && $model->no_faktur === null) {
              $prefix = 'INV/' . date('Y') . '/' . date('m') . '/';
              $lastFaktur = Pemasukan::find()
                ->where(['like', 'no_faktur', $prefix . '%', false])
                ->andWhere(['deleted_at' => null])
                ->orderBy(['pemasukan_id' => SORT_DESC])
                ->one();

              $nextNum = ($lastFaktur && preg_match('/\/(\d{3})$/', $lastFaktur->no_faktur, $m))
                ? ((int)$m[1] + 1)
                : 1;

              $model->no_faktur = $prefix . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
              $model->save(false); // simpan no faktur ke parent
            }

            $transaction->commit();
            return ['status' => 'success', 'message' => 'Pemasukan berhasil disimpan'];
          }
        } catch (\Exception $e) {
          $transaction->rollBack();
          return ['status' => 'failed', 'message' => 'Gagal menyimpan data', 'errors' => $e->getMessage()];
        }
      }
    }

    return $this->renderAjax('create', compact('model', 'akunPemasukanList'));
  }

  /**
   * Helper function to create PemasukanCicilan records.
   * @param Pemasukan $pemasukan The parent Pemasukan model.
   */
  protected function createInstallmentRecords(Pemasukan $pemasukan)
  {
    $totalInstallments = $pemasukan->cicilan;
    if ($totalInstallments <= 0) {
      return;
    }

    // Hitung interval bulan berdasarkan jumlah cicilan (misal 3 → setiap 4 bulan)
    $intervalMonths = floor(12 / $totalInstallments);
    $baseDueDate = new \DateTime($pemasukan->purchase_date);

    $anakPemasukans = Pemasukan::find()
      ->where(['parent_id' => $pemasukan->pemasukan_id])
      ->orderBy(['tanggal' => SORT_ASC])
      ->all();

    foreach ($anakPemasukans as $index => $child) {
      $cicilanModel = new PemasukanCicilan();
      $cicilanModel->pemasukan_id = $child->pemasukan_id;
      $cicilanModel->ke = $index + 1;
      $cicilanModel->status = 'Menunggu';

      $dueDate = clone $baseDueDate;
      $dueDate->modify('+' . ($index * $intervalMonths) . ' months');
      $cicilanModel->jatuh_tempo = $dueDate->format('Y-m-d');

      if (!$cicilanModel->save()) {
        Yii::error("Gagal menyimpan cicilan ke-" . ($index + 1) . ": " . json_encode($cicilanModel->getErrors()));
      }
    }
  }

  public function actionGetDealsData($id)
  {
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

    $deals = \app\models\Deals::find()
      ->with(['customer', 'product'])
      ->where([
        'deals_id' => $id,
        'label_deals' => 'Deal Won'  // ✅ Filter hanya deal yang Deal Won
      ])
      ->one();

    if (!$deals) return ['error' => 'Data deals tidak ditemukan'];
    if (!$deals->product) return ['error' => 'Produk tidak ditemukan'];
    if (!$deals->customer) return ['error' => 'Customer tidak ditemukan'];

    return [
      'customer_name' => $deals->customer->customer_name,
      'customer_email' => $deals->customer->customer_email,
      'product_name' => $deals->product->product_name,
      'unit' => $deals->unit_product ?? 1,
      'price_product' => $deals->price_product ?? 0,
      'total' => $deals->total,
      'purchase_type' => $deals->purchase_type,
    ];
  }

  public function actionUpdate($pemasukan_id)
  {
    $model = Pemasukan::find()
      ->with([
        'deals.customer',
        'deals.product',
        'accountkeluar',
        'cicilans'
      ])
      ->where(['pemasukan_id' => $pemasukan_id])
      ->one();

    if (!$model) {
      throw new NotFoundHttpException('Data tidak ditemukan.');
    }

    $akunPemasukanList = Accountkeluar::find()
      ->where(['ilike', 'penggunaan', 'pemasukan'])
      ->andWhere([
        'or',
        ['like', 'code', '4-%', false],
        ['like', 'code', '7-%', false],
      ])
      ->orderBy(['akun' => SORT_ASC])
      ->all();

    $isEdit = Yii::$app->request->get('edit') === '1';

    // Inject data virtual untuk ditampilkan di form
    if ($model->deals) {
      $model->penerima_nama = $model->deals->customer->customer_name ?? null;
      $model->penerima_email = $model->deals->customer->customer_email ?? null;
      $model->produk = $model->deals->product->product_name ?? null;
      $model->unit = $model->deals->unit_product ?? null;
      $model->price_product = $model->deals->price_product ?? null;
      $model->purchase_type = $model->deals->purchase_type ?? null;
    }

    // Hitung total pembayaran cicilan saat ini
    $pembayaranSaatIni = 0;
    if (!empty($model->cicilans)) {
      foreach ($model->cicilans as $cicilan) {
        $pembayaranSaatIni += $cicilan->nominal;
      }
    }

    // Tangani proses POST
    if ($model->load(Yii::$app->request->post())) {
      if ($model->save()) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
          'status' => 'success',
          'message' => 'Data berhasil diperbarui'
        ];
      } else {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
          'status' => 'failed',
          'message' => 'Gagal menyimpan data',
          'errors' => $model->getErrors(),
        ];
      }
    }

    // Format tanggal (saat GET request)
    if (!Yii::$app->request->isPost) {
      if ($model->purchase_date) {
        $model->purchase_date = Yii::$app->formatter->asDate($model->purchase_date, 'php:d/m/Y');
      }
      if ($model->tgl_jatuhtempo) {
        $model->tgl_jatuhtempo = Yii::$app->formatter->asDate($model->tgl_jatuhtempo, 'php:d/m/Y');
      }
    }

    // Render AJAX form
    Yii::$app->response->format = \yii\web\Response::FORMAT_HTML;
    if (Yii::$app->request->isAjax) {
      $this->layout = false;
    }

    return $this->renderAjax('update', [
      'model' => $model,
      'akunPemasukanList' => $akunPemasukanList,
      'isEdit' => $isEdit,
      'pembayaranSaatIni' => $pembayaranSaatIni,
    ]);
  }

  public function actionDelete($pemasukan_id)
  {
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

    $model = $this->findModel($pemasukan_id);
    return $model->delete()
      ? ['status' => 'success', 'message' => 'Data berhasil dihapus']
      : ['status' => 'failed', 'message' => 'Gagal menghapus data'];
  }

  public function actionGenerateFaktur()
  {
    $tanggalSekarang = date('Y-m-d');
    $tanggalJatuhTempo = date('Y-m-t', strtotime($tanggalSekarang));

    $list = Pemasukan::find()
      ->andWhere(['<=', 'purchase_date', $tanggalSekarang])
      ->all();

    foreach ($list as $pemasukan) {
      $pemasukan->no_faktur = Pemasukan::generateNoFaktur();
      $pemasukan->tgl_jatuhtempo = $tanggalJatuhTempo;

      $pemasukan->save(false);
    }

    Yii::$app->session->setFlash('success', "Faktur berhasil digenerate .");
    return $this->redirect(['index']);
  }

  public function actionSavePaymentType($id)
  {
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

    $model = $this->findModel($id);
    $tipe = Yii::$app->request->post('tipe');

    if (!in_array($tipe, ['transfer', 'cash'])) {
      return ['status' => 'error', 'message' => 'Tipe pembayaran tidak valid.'];
    }

    $model->tipe_pembayaran = $tipe;

    if ($model->save(false, ['tipe_pembayaran'])) {
      return ['status' => 'success', 'message' => 'Tipe pembayaran berhasil disimpan.'];
    } else {
      return ['status' => 'error', 'message' => 'Gagal menyimpan data.'];
    }
  }

  public function actionUpdateStatusOtomatis()
  {
    $list = Pemasukan::find()
      ->where(['!=', 'status', 'Lunas'])
      ->andWhere(['<=', 'tgl_jatuhtempo', date('Y-m-d')])
      ->all();

    foreach ($list as $pemasukan) {
      if ($pemasukan->status === 'Menunggu Pembayaran' && strtotime($pemasukan->tgl_jatuhtempo) < time()) {
        $pemasukan->status = 'Telat Bayar';
        $pemasukan->save(false);
      }
    }

    return "Update status selesai.";
  }

  public function actionSearchDeals($q = null)
  {
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

    $query = \app\models\Deals::find()
      ->joinWith('customer')
      ->where(['like', 'customer.customer_name', $q])
      ->limit(20)
      ->all();

    $results = [];
    foreach ($query as $deal) {
      $results[] = [
        'id' => $deal->deals_id,
        'text' => $deal->customer->customer_name ?? '(Tanpa Customer)',
      ];
    }

    return ['results' => $results];
  }

  public function actionInvoice($id)
  {
    $model = Pemasukan::find()
      ->with(['accountkeluar', 'deals.customer'])
      ->where(['pemasukan_id' => $id])
      ->one();

    if (!$model) {
      throw new NotFoundHttpException('Data tidak ditemukan.');
    }

    return $this->render('invoice', [
      'model' => $model,
    ]);
  }

  public function actionUploadBukti($id)
  {
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    /** @var app\models\Pemasukan $model */
    $model = Pemasukan::findOne($id);
    if (!$model) {
      return ['status' => 'failed', 'message' => 'Data pemasukan tidak ditemukan'];
    }
    return ['status' => 'failed', 'message' => 'This action is deprecated for payment processing. Use actionProcessPenerimaanPembayaran.'];
  }

  public function actionSendInvoice($id)
  {
    Yii::$app->response->format = Response::FORMAT_JSON;
    /** @var app\models\Pemasukan $model */
    $model = Pemasukan::findOne($id);

    if (!$model) {
      return ['status' => 'failed', 'message' => 'Data pemasukan tidak ditemukan.'];
    }

    /* ①  render view => HTML */
    $html = $this->renderPartial('invoice_pdf', ['model' => $model]);

    /* ②  path file PDF */
    $cleanFaktur = str_replace(['/', '\\'], '-', $model->no_faktur); // ganti / dan \ jadi -
    $pdfFile = 'Invoice_' . $cleanFaktur . '.pdf';
    $pdfDir    = Yii::getAlias('@app/modules/vendorfinance/views/pemasukan/invoicepdf/');
    if (!is_dir($pdfDir)) {
      mkdir($pdfDir, 0777, true);
    }
    $fullPath = $pdfDir . $pdfFile;

    /* ③  generate PDF */
    $mpdf = new Mpdf(['format' => 'A4']);
    $mpdf->WriteHTML($html);
    $mpdf->Output($fullPath, \Mpdf\Output\Destination::FILE);

    if (!is_file($fullPath)) {
      return ['status' => 'failed', 'message' => 'Gagal membuat file PDF invoice.'];
    }
    try {
      $sent = Yii::$app->mailer
        ->compose()
        ->setFrom(['info@bigsgroup.co.id' => 'PT Bigs Integrasi Teknologi'])
        ->setTo($model->deals->customer->customer_email)
        ->setSubject('Invoice ' . $model->no_faktur)
        ->setTextBody(
          "Yth. {$model->deals->customer->customer_name},\n\n" .
            "Berikut kami lampirkan invoice pembayaran nomor {$model->no_faktur}.\n\n" .
            "Salam,\nPT Bigs Integrasi Teknologi"
        )
        ->attach($fullPath, ['fileName' => $pdfFile])
        ->send();

      return [
        'status' => $sent ? 'success' : 'failed',
        'message' => $sent
          ? 'Invoice berhasil dikirim ke email customer.'
          : 'Gagal mengirim email. Silakan periksa SMTP / alamat email.',
      ];
    } catch (\Exception $e) {
      // 🔥 Tampilkan pesan error ke response JSON
      return [
        'status' => 'failed',
        'message' => 'Exception saat kirim email: ' . $e->getMessage(),
      ];
    }
    /* ④  kirim e-mail (duplicate, removed the previous try-catch block and moved it inside) */
  }

  public function actionPrintInvoice($id)
  {
    $this->layout = false;
    $model = $this->findModel($id);
    $setting = \app\models\Pengaturanakun::findOne(1);

    return $this->render('invoice', [
      'model' => $model,
      'setting' => $setting
    ]);
  }

  public function actionInvoiceView($id)
  {
    $model = Pemasukan::find()
      ->with(['deals.customer', 'deals.product', 'accountkeluar'])
      ->where(['pemasukan_id' => $id])
      ->one();
    if (!$model) {
      throw new NotFoundHttpException("Data pemasukan tidak ditemukan.");
    }

    return $this->render('invoice_view', [ // view yang sesuai seperti canvas
      'model' => $model,
    ]);
  }

  public function actionPenerimaanPembayaran($id)
  {
    $pemasukan = Pemasukan::findOne($id);
    if (!$pemasukan) {
      throw new \yii\web\NotFoundHttpException('Invoice tidak ditemukan.');
    }

    $model = new PenerimaanPembayaran();
    $model->pemasukan_id = $pemasukan->pemasukan_id;

    $cicilanAktif = null;
    foreach ($pemasukan->cicilans as $cicilan) {
      if ($cicilan->status !== 'Lunas') {
        $cicilanAktif = $cicilan;
        $model->pemasukan_cicilan_id = $cicilan->id;
        break;
      }
    }
    // Ambil akun pemasukan (hanya kode 4- dan 7-)
    $akunPemasukanList = Accountkeluar::find()
      ->where(['ilike', 'penggunaan', 'pemasukan'])
      ->andWhere([
        'or',
        ['like', 'code', '4-%', false],
        ['like', 'code', '7-%', false],
      ])
      ->orderBy(['akun' => SORT_ASC])
      ->all();

    return $this->renderAjax('_penerimaan_pembayaran', [
      'model' => $model,
      'pemasukan' => $pemasukan,
      'akunPemasukanList' => $akunPemasukanList,
      'cicilanAktif' => $cicilanAktif, // ✅ tambahan ini
    ]);
  }

  public function actionProcessPenerimaanPembayaran($pemasukan_id)
  {
    $pemasukan = Pemasukan::findOne($pemasukan_id);
    if (!$pemasukan) {
      throw new NotFoundHttpException('Data pemasukan tidak ditemukan.');
    }

    $model = new PenerimaanPembayaran();
    $model->pemasukan_id = $pemasukan->pemasukan_id;

    if (Yii::$app->request->isPost) {
      $post = Yii::$app->request->post();
      $model->load($post);

      // Format tanggal
      if (!empty($model->tanggal_bukti_transfer)) {
        $model->tanggal_bukti_transfer = date('Y-m-d', strtotime(str_replace('/', '-', $model->tanggal_bukti_transfer)));
      }

      // Simpan tipe pembayaran & akun
      $pemasukan->tipe_pembayaran = $model->tipe_pembayaran;
      $pemasukan->accountkeluar_id = $model->accountkeluar_id;

      // === Upload bukti ===
      $file = UploadedFile::getInstance($model, 'bukti_transfer');
      if ($file) {
        $ext = $file->getExtension();
        $filename = 'bukti_' . time() . '.' . $ext;

        $folder = $model->pemasukan_cicilan_id
          ? '@app/modules/vendorfinance/views/pemasukan/bukti_cicilan/'
          : '@app/modules/vendorfinance/views/pemasukan/bukti/';

        $savePath = Yii::getAlias($folder) . $filename;

        if (!is_dir(Yii::getAlias($folder))) {
          mkdir(Yii::getAlias($folder), 0777, true);
        }

        if ($file->saveAs($savePath)) {
          $model->bukti_transfer = $filename;
        } else {
          return $this->asJson([
            'success' => false,
            'message' => 'Gagal menyimpan file bukti transfer.'
          ]);
        }
      }

      $model->tanggal_bukti_transfer = $model->tanggal_bukti_transfer ?: date('Y-m-d');

      // === Hitung jumlah efektif masuk ===
      $potongan = $model->potongan_pajak ?? 0;
      $jumlahEfektif = $model->jumlah_terbayar - $potongan;
      if ($jumlahEfektif < 0) {
        $jumlahEfektif = 0;
      }

      // Ambil sisa tagihan berdasarkan parent-child
      $sisaTagihan = $pemasukan->getSisaTagihan();

      if ($model->pemasukan_cicilan_id) {
        $cicilan = \app\models\PemasukanCicilan::findOne($model->pemasukan_cicilan_id);

        // Overpayment check
        if ($jumlahEfektif > $sisaTagihan) {
          return $this->asJson([
            'success' => false,
            'message' => 'Jumlah pembayaran melebihi total tagihan.'
          ]);
        }

        // Hitung cicilan yang belum lunas (parent + child)
        $cicilanBelumLunas = \app\models\PemasukanCicilan::find()
          ->where(['pemasukan_id' => $this->getParentAndChildIds($pemasukan)])
          ->andWhere(['!=', 'status', 'Lunas'])
          ->count();

        // Cicilan terakhir harus lunas
        if ($cicilanBelumLunas === 1) {
          $totalSetelahBayar = ($pemasukan->getJumlahTerbayar() + $jumlahEfektif)
            + ($potongan + $this->getTotalPotonganSebelumnya($pemasukan));

          if ($totalSetelahBayar < $pemasukan->grand_total) {
            return $this->asJson([
              'success' => false,
              'message' => 'Cicilan terakhir harus dibayar lunas sebesar ' .
                Yii::$app->formatter->asCurrency($sisaTagihan)
            ]);
          }
        }
      }

      if ($model->validate()) {
        if ($model->save(false)) {
          // Update cicilan jika ada
          if ($model->pemasukan_cicilan_id && isset($cicilan)) {
            $cicilan->status = 'Lunas';
            $cicilan->tanggal_bayar = $model->tanggal_bukti_transfer;
            $cicilan->bukti_path = $model->bukti_transfer;
            $cicilan->nominal = $jumlahEfektif;
            $cicilan->save(false);
          }

          // Update sisa tagihan parent/child
          $pemasukan->sisa_tagihan = $pemasukan->getSisaTagihan();
          $pemasukan->status = ($pemasukan->sisa_tagihan <= 0) ? 'Lunas' : 'Berjalan';
          $pemasukan->save(false);

          // Jika ini child → update parent
          if ($pemasukan->parent_id) {
            $parent = Pemasukan::findOne($pemasukan->parent_id);
            if ($parent) {
              $parent->sisa_tagihan = $parent->getSisaTagihan();
              $parent->status = ($parent->sisa_tagihan <= 0) ? 'Lunas' : 'Berjalan';
              $parent->save(false);
            }
          }

          return $this->asJson([
            'success' => true,
            'message' => 'Pembayaran berhasil disimpan.'
          ]);
        }
      }

      return $this->asJson([
        'success' => false,
        'message' => 'Validasi gagal. Periksa kembali input Anda.'
      ]);
    }

    return $this->renderAjax('_penerimaan_pembayaran', [
      'model' => $model,
      'pemasukan' => $pemasukan,
      'akunPemasukanList' => $this->getAkunList(),
      'cicilanAktif' => $this->getCicilanAktif($pemasukan->pemasukan_id),
    ]);
  }

  protected function getTotalPotonganSebelumnya($pemasukan)
  {
    return (float) PenerimaanPembayaran::find()
      ->where(['pemasukan_id' => $pemasukan->pemasukan_id])
      ->sum('COALESCE(potongan_pajak,0)');
  }
  /**
   * Helper: Ambil ID parent dan semua child
   */
  protected function getParentAndChildIds($pemasukan)
  {
    $ids = [$pemasukan->pemasukan_id];

    // Jika parent → ambil semua child
    $childIds = Pemasukan::find()
      ->select('pemasukan_id')
      ->where(['parent_id' => $pemasukan->pemasukan_id])
      ->column();

    if (!empty($childIds)) {
      $ids = array_merge($ids, $childIds);
    }

    // Jika child → ambil parent + semua child
    if ($pemasukan->parent_id) {
      $parentId = $pemasukan->parent_id;
      $ids = [$parentId];
      $childIds = Pemasukan::find()
        ->select('pemasukan_id')
        ->where(['parent_id' => $parentId])
        ->column();
      $ids = array_merge($ids, $childIds);
    }

    return $ids;
  }

  private function getAkunList()
  {
    return \app\models\Accountkeluar::find()
      ->where(['ilike', 'penggunaan', 'pemasukan'])
      ->andWhere([
        'or',
        ['like', 'code', '4-%', false],
        ['like', 'code', '7-%', false],
      ])
      ->orderBy(['akun' => SORT_ASC])
      ->all();
  }

  private function getCicilanAktif($pemasukan_id)
  {
    return \app\models\PemasukanCicilan::find()
      ->where(['pemasukan_id' => $pemasukan_id])
      ->andWhere(['!=', 'status', 'Lunas'])
      ->orderBy(['ke' => SORT_ASC])
      ->one();
  }

  public function actionGetBukti($filename)
  {
    $path = Yii::getAlias('@app/modules/vendorfinance/views/pemasukan/bukti/') . $filename;
    // Check for new upload path
    $newPath = 'uploads/payment_proofs/' . $filename;
    if (file_exists($newPath)) {
      $path = $newPath;
    } else if (!file_exists($path)) {
      throw new \yii\web\NotFoundHttpException("File tidak ditemukan");
    }

    // Cek tipe file
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    $inline = in_array($ext, ['pdf', 'jpg', 'jpeg', 'png']);

    return Yii::$app->response->sendFile($path, $filename, [
      'inline' => $inline
    ]);
  }

  public function actionBayarCicilan($cicilan_id)
  {
    Yii::$app->response->format = Response::FORMAT_JSON;

    $cicilan = PemasukanCicilan::findOne($cicilan_id);
    if (!$cicilan || $cicilan->status === 'Lunas') {
      return ['status' => 'failed', 'message' => 'Cicilan tidak ditemukan / sudah lunas'];
    }

    $pemasukan = $cicilan->pemasukan;

    $totalGrand = $pemasukan->grand_total;
    $totalPaid = (new \yii\db\Query())
      ->from('penerimaan_pembayaran')
      ->where(['pemasukan_id' => $pemasukan->pemasukan_id])
      ->sum('jumlah_terbayar');

    $sisaTagihan = $totalGrand - $totalPaid;

    // Hitung sisa cicilan yang belum lunas
    $jumlahCicilanTersisa = PemasukanCicilan::find()
      ->where(['pemasukan_id' => $pemasukan->pemasukan_id, 'status' => 'Menunggu'])
      ->count();

    if ($jumlahCicilanTersisa <= 1) {
      $nominalCicilan = $sisaTagihan; // Bayar sisa penuh
    } else {
      $nominalCicilan = round($sisaTagihan / $jumlahCicilanTersisa, 2);
    }

    $penerimaanModel = new PenerimaanPembayaran();
    $penerimaanModel->pemasukan_id = $pemasukan->pemasukan_id;
    $penerimaanModel->pemasukan_cicilan_id = $cicilan->id;
    $penerimaanModel->tanggal_bukti_transfer = date('Y-m-d');
    $penerimaanModel->metode_pembayaran = 'Manual';
    $penerimaanModel->jumlah_terbayar = $nominalCicilan;
    $penerimaanModel->deskripsi = 'Pembayaran cicilan ke-' . $cicilan->ke . ' (manual)';

    if ($penerimaanModel->save()) {
      $cicilan->status = 'Lunas';
      $cicilan->tanggal_bayar = $penerimaanModel->tanggal_bukti_transfer;
      $cicilan->nominal = $nominalCicilan; // ← simpan nominal saat bayar
      $cicilan->bukti_path = $penerimaanModel->bukti_transfer;
      $cicilan->save(false);

      $pemasukan->sisa_tagihan -= $nominalCicilan;
      if ($pemasukan->sisa_tagihan <= 0) {
        $pemasukan->sisa_tagihan = 0;
        $pemasukan->status = 'Lunas';
      } else {
        $pemasukan->status = 'Berjalan';
      }
      $pemasukan->save(false);

      return ['status' => 'success', 'message' => 'Cicilan ke-' . $cicilan->ke . ' berhasil dibayar'];
    }

    Yii::error("Gagal menyimpan pembayaran cicilan: " . json_encode($penerimaanModel->getErrors()));
    return ['status' => 'failed', 'message' => 'Gagal mencatat pembayaran cicilan'];
  }

  public function actionCicilanSummary($customerId)
  {
    Yii::$app->response->format = Response::FORMAT_JSON;

    return (new \yii\db\Query)
      ->select([
        'p.pemasukan_id',
        'p.no_faktur',
        'p.cicilan',
        'sudah_bayar' => 'SUM(CASE WHEN pc.status=\'Lunas\' THEN 1 ELSE 0 END)',
        'sisa_cicilan' => 'SUM(CASE WHEN pc.status!=\'Lunas\' THEN 1 ELSE 0 END)',
      ])
      ->from('pemasukan p')
      ->innerJoin('pemasukan_cicilan pc', 'pc.pemasukan_id = p.pemasukan_id')
      ->where(['p.customer_id' => $customerId])
      ->groupBy(['p.pemasukan_id', 'p.no_faktur', 'p.cicilan'])
      ->all();
  }

  public function actionBukti($file)
  {
    $path = Yii::getAlias('@app/modules/vendorfinance/views/pemasukan/bukti/') . $file;
    if (file_exists($path)) {
      return Yii::$app->response->sendFile($path);
    }
    throw new \yii\web\NotFoundHttpException('File tidak ditemukan.');
  }

  public function actionBuktiCicilan($file)
  {
    $path = Yii::getAlias('@app/modules/vendorfinance/views/pemasukan/bukti_cicilan/' . $file);

    if (file_exists($path)) {
      return Yii::$app->response->sendFile($path);
    }

    throw new \yii\web\NotFoundHttpException('File tidak ditemukan.');
  }

  public function actionViewBukti($file, $cicilan = false)
  {
    $basePath = Yii::getAlias('@app/modules/vendorfinance/views/pemasukan/');
    $folder = $cicilan ? 'bukti_cicilan' : 'bukti';
    $fullPath = $basePath . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $file;

    if (!file_exists($fullPath)) {
      throw new \yii\web\NotFoundHttpException('File tidak ditemukan: ' . $fullPath);
    }

    $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
    $inline = in_array($ext, ['pdf', 'jpg', 'jpeg', 'png']);

    return Yii::$app->response->sendFile($fullPath, $file, [
      'inline' => $inline
    ]);
  }

  public function beforeValidate()
  {
    if ($this->parent_id === null) {
      $this->no_faktur = null; // pastikan kosong
    }

    return parent::beforeValidate();
  }

  protected function findModel($pemasukan_id)
  {
    if (($model = Pemasukan::findOne(['pemasukan_id' => $pemasukan_id])) !== null) {
      return $model;
    }

    throw new NotFoundHttpException('Halaman tidak ditemukan.');
  }
}
