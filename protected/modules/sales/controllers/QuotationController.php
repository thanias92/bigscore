<?php

namespace app\modules\sales\controllers;

use Yii;
use app\models\Quotation;
use app\models\Customer;
use app\models\Product;
use app\models\Deals;
use app\models\DealQuotations;
use app\modules\sales\QuotationSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use Mpdf\Mpdf;
use yii\helpers\ArrayHelper;

class QuotationController extends Controller
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

  public function actionIndex()
  {
    $searchModel = new QuotationSearch();
    $dataProvider = $searchModel->search($this->request->queryParams);

    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ]);
  }

  public function actionCreate()
  {
    $model = new Quotation();

    if ($model->isNewRecord) {
      $model->created_date = date('Y-m-d');
      $model->expiration_date = date('Y-m-d', strtotime('+7 days'));
    }

    // Generate quotation_code
    $lastQuotation = Quotation::find()
      ->where(['like', 'quotation_code', 'QUO-%', false])
      ->andWhere(['deleted_at' => null])
      ->orderBy(['quotation_id' => SORT_DESC])
      ->one();

    if ($lastQuotation && preg_match('/QUO-(\d+)/', $lastQuotation->quotation_code, $matches)) {
      $lastNumber = (int)$matches[1];
      $newNumber = $lastNumber + 1;
      $model->quotation_code = 'QUO-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    } else {
      $model->quotation_code = 'QUO-001';
    }

    $customers = Customer::find()->all();
    $products = Product::find()->all();

    // Ambil daftar deals dengan nama customer (eager loading)
    $deals = Deals::find()->with('customer')->where(['deleted_at' => null])->all();
    $dealsList = ArrayHelper::map($deals, 'deals_id', function ($deal) {
      return $deal->deals_code . ' - ' . ($deal->customer->customer_name ?? 'N/A');
    });

    // // Gunakan closure untuk memformat teks dropdown
    // $dealsList = ArrayHelper::map($deals, 'deals_id', function ($deal) {
    //   // Tampilkan kode deal diikuti nama customer
    //   return $deal->deals_code . ' - ' . ($deal->customer->customer_name ?? 'No Customer');
    // });

    if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
      Yii::$app->response->format = Response::FORMAT_JSON;

      // Lakukan type casting ke integer dan float sebelum perkalian
      $model->total = (int)$model->unit_product * (float)$model->price_product;
      $model->optional_total = (int)$model->optional_unit_product * (float)$model->optional_price_product;

      if ($model->save()) {
        if (!empty($model->linked_deal_id)) {
          $junction = new DealQuotations();
          $junction->deal_id = $model->linked_deal_id;
          $junction->quotation_id = $model->quotation_id;
          $junction->is_active = true;
          $junction->save();
        }
        return ['success' => true, 'message' => 'Data berhasil ditambahkan'];
      } else {
        return ['success' => false, 'message' => 'Gagal menyimpan data', 'errors' => $model->getErrors()];
      }
    }

    return $this->renderAjax('_form', [
      'model' => $model,
      'customers' => $customers,
      'products' => $products,
      'dealsList' => $dealsList,
      'mode' => 'create',
    ]);
  }

  public function actionView($quotation_id)
  {
    $model = $this->findModel($quotation_id);
    $customers = Customer::find()->all();
    $products = Product::find()->all();

    $dealsList = [];
    if ($model->activeDeal) {
      $dealsList[$model->activeDeal->deals_id] = $model->activeDeal->deals_code . ' - ' . ($model->activeDeal->customer->customer_name ?? 'N/A');
    }

    return $this->renderAjax('_form', [
      'model' => $model,
      'customers' => $customers,
      'products' => $products,
      'dealsList' => $dealsList,
      'mode' => 'view',
    ]);
  }

  public function actionUpdate($quotation_id)
  {
    $model = $this->findModel($quotation_id);
    $customers = Customer::find()->all();
    $products = Product::find()->all();

    $deals = Deals::find()->with('customer')->where(['deleted_at' => null])->all();
    $dealsList = ArrayHelper::map($deals, 'deals_id', function ($deal) {
      return $deal->deals_code . ' - ' . ($deal->customer->customer_name ?? 'N/A');
    });

    if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
      Yii::$app->response->format = Response::FORMAT_JSON;

      // Lakukan type casting ke integer dan float sebelum perkalian
      $model->total = (int)$model->unit_product * (float)$model->price_product;
      $model->optional_total = (int)$model->optional_unit_product * (float)$model->optional_price_product;

      if ($model->save()) {
        // Logika untuk mengupdate hubungan
        if (!empty($model->linked_deal_id)) {
          // 1. Non-aktifkan semua link yang mungkin ada untuk quotation ini
          DealQuotations::updateAll(['is_active' => false], ['quotation_id' => $model->quotation_id]);

          // 2. Cari atau buat link yang baru dan aktifkan
          $junction = DealQuotations::findOne(['deal_id' => $model->linked_deal_id, 'quotation_id' => $model->quotation_id]) ?? new DealQuotations();
          $junction->deal_id = $model->linked_deal_id;
          $junction->quotation_id = $model->quotation_id;
          $junction->is_active = true;
          $junction->save();
        }
        return ['success' => true, 'message' => 'Data has been updated successfully'];
      } else {
        return ['success' => false, 'message' => 'Failed to save changes', 'errors' => $model->getErrors()];
      }
    }

    return $this->renderAjax('_form', [
      'model' => $model,
      'customers' => $customers,
      'products' => $products,
      'dealsList' => $dealsList,
      'mode' => 'edit',
    ]);
  }

  public function actionDelete($quotation_id)
  {
    $model = $this->findModel($quotation_id);
    Yii::$app->response->format = Response::FORMAT_JSON;
    if ($model->delete()) {
      return ['status' => 'success', 'message' => 'Data berhasil dihapus'];
    } else {
      return ['status' => 'failed', 'message' => 'Gagal menghapus data'];
    }
  }

  protected function findModel($quotation_id)
  {
    if (($model = Quotation::findOne(['quotation_id' => $quotation_id])) !== null) {
      return $model;
    }
    throw new NotFoundHttpException('The requested page does not exist.');
  }

  public function actionGetInfo($id)
  {
    Yii::$app->response->format = Response::FORMAT_JSON;
    $customer = Customer::findOne($id);
    return ['email' => $customer->customer_email ?? ''];
  }

  public function actionGetProductInfo($id)
  {
    Yii::$app->response->format = Response::FORMAT_JSON;
    $product = Product::findOne($id);
    if ($product) {
      return ['unit' => $product->unit, 'price' => $product->harga];
    }
    return ['unit' => '', 'price' => ''];
  }

  public function actionPrintView($quotation_id)
  {
    $model = $this->findModel($quotation_id);
    $setting = \app\models\Pengaturanakun::findOne(1);

    // LOGIKA UNTUK MEMBUAT PATH LOGO DITAMBAHKAN DI SINI
    $logoPath = null;
    if ($setting && $setting->logo) {
      // @webroot menunjuk ke folder /web di proyek Anda secara absolut
      $logoPath = Yii::getAlias('@webroot/uploads/logo/' . $setting->logo);
      // Pastikan file benar-benar ada
      if (!is_file($logoPath)) {
        $logoPath = null;
      }
    }

    // KIRIM VARIABEL $logoPath KE VIEW
    return $this->render('quotation_print', [
      'model' => $model,
      'setting' => $setting,
      'logoPath' => $logoPath, // <-- Variabel baru ditambahkan di sini
    ]);
  }

  public function actionDownloadPdf($id)
  {
    $model = $this->findModel($id);
    $setting = \app\models\Pengaturanakun::findOne(1); // Ambil data setting

    // BUAT PATH ABSOLUT UNTUK LOGO
    $logoPath = null;
    if ($setting && $setting->logo) {
      $logoPath = Yii::getAlias('@webroot/uploads/logo/' . $setting->logo);
      if (!is_file($logoPath)) {
        $logoPath = null; // Set null jika file tidak ada
      }
    }

    session_write_close();

    // KIRIM PATH LOGO KE VIEW
    $content = $this->renderPartial('quotation_pdf', [
      'model' => $model,
      'setting' => $setting,
      'logoPath' => $logoPath, // <-- Kirim path absolut
    ]);

    $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
    $mpdf->WriteHTML($content);
    $filename = 'Quotation-' . $model->quotation_code . '.pdf';
    return $mpdf->Output($filename, 'D');
  }

  public function actionPrepareEmail($quotation_id)
  {
    $model = $this->findModel($quotation_id);
    $model->quotation_status = 'Sent';
    if ($model->save(false, ['quotation_status'])) {
      $history = new \app\models\QuotationHistory();
      $history->quotation_id = $model->quotation_id;
      $history->activity_type = 'notification';
      $history->description = "User memulai pengiriman email manual via Gmail. Status diubah menjadi 'Sent'.";
      $history->created_by = Yii::$app->user->id ?? null;
      $history->save(false);
    }
    $to = $model->customer->customer_email;
    $subject = 'Quotation from PT Bigs Integrasi Teknologi - Ref: ' . $model->quotation_code;
    $body = "Dear {$model->customer->customer_name},\n\n"
      . "Following up on our recent conversation, I am pleased to attach the quotation for your review, with reference number {$model->quotation_code}.\n\n"
      . "This document outlines the specifics of our proposal. We are confident that it meets the requirements you've shared with us.\n\n"
      . "Please feel free to reach out if you have any questions or require further clarification. We look forward to the possibility of working together.\n\n"
      . "Best regards,\n\n"
      . "Rasmi Gumilang\n"
      . "Sales Representative\n"
      . "PT Bigs Integrasi Teknologi\n"
      . "081276017962\n"
      . "https://www.bigs.id/";
    $gmailLink = "https://mail.google.com/mail/?view=cm&fs=1"
      . "&to=" . rawurlencode($to)
      . "&su=" . rawurlencode($subject)
      . "&body=" . rawurlencode($body);
    return $this->redirect($gmailLink);
  }

  public function actionSendEmailWithAttachment()
  {
    $quotation_id = Yii::$app->request->post('quotation_id');
    Yii::$app->response->format = Response::FORMAT_JSON;
    if (!$quotation_id) {
      return ['success' => false, 'message' => 'Error: Quotation ID tidak ditemukan.'];
    }
    $model = $this->findModel($quotation_id);
    try {
      $htmlContent = $this->renderPartial('quotation_pdf', ['model' => $model]);
      $mpdf = new Mpdf(['format' => 'A4']);
      $mpdf->WriteHTML($htmlContent);
      $pdfString = $mpdf->Output('', \Mpdf\Output\Destination::STRING_RETURN);
      $pdfFileName = 'Quotation_' . preg_replace('/[^a-zA-Z0-9\-]/', '_', $model->quotation_code) . '.pdf';
      $mailSent = Yii::$app->mailer->compose()
        ->setFrom(['info@bigsgroup.co.id' => 'PT Bigs Integrasi Teknologi'])
        ->setTo($model->customer->customer_email)
        ->setSubject('Penawaran Harga (Quotation) - ' . $model->quotation_code)
        ->setTextBody("Yth. {$model->customer->customer_name},\n\nBerikut kami lampirkan penawaran harga (quotation) dengan nomor {$model->quotation_code}.\n\nTerima kasih.\nSalam,\nPT Bigs Integrasi Teknologi")
        ->attachContent($pdfString, ['fileName' => $pdfFileName, 'contentType' => 'application/pdf'])
        ->send();
      if (!$mailSent) {
        return ['success' => false, 'message' => 'Gagal mengirim email. Periksa log aplikasi.'];
      }
      $model->quotation_status = 'Sent';
      if ($model->save(false, ['quotation_status'])) {
        $history = new \app\models\QuotationHistory();
        $history->quotation_id = $model->quotation_id;
        $history->activity_type = 'notification';
        $history->description = "Quotation dikirim melalui email ke " . $model->customer->customer_name . " (" . $model->customer->customer_email . ").";
        $history->created_by = Yii::$app->user->id ?? null;
        $history->save(false);
        return ['success' => true, 'message' => 'Email berhasil dikirim dan status telah diubah menjadi Sent.'];
      } else {
        return ['success' => false, 'message' => 'Email terkirim, namun gagal mengubah status quotation.'];
      }
    } catch (\Exception $e) {
      return ['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()];
    }
  }
}
