<?php

namespace app\modules\sales\controllers;

use Yii;
use app\models\Deals;
use app\models\Customer;
use app\models\Product;
use app\models\Quotation;
use app\models\DealQuotations;
use app\models\DealsHistory;
use app\modules\sales\DealsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class DealsController extends Controller
{
  public function behaviors()
  {
    return array_merge(
      parent::behaviors(),
      [
        'verbs' => [
          'class' => VerbFilter::class,
          'actions' => [
            'delete' => ['POST'],
            'view-modal' => ['GET', 'POST'],
            'send-quotation' => ['POST'],
          ],
        ],
      ]
    );
  }

  public function actionIndex()
  {
    $searchModel = new DealsSearch();
    $dataProvider = $searchModel->search($this->request->queryParams);

    $model = new Deals();

    // Gunakan getDealsLabelList() dari model Deals
    $dealsLabels = array_keys(Deals::getDealsLabelList());

    // Ambil semua deals dari dataProvider
    $deals = $dataProvider->getModels();

    // Kelompokkan deals per label
    $dealsByLabel = [];
    foreach ($dealsLabels as $label) {
      $dealsByLabel[$label] = [];
    }

    foreach ($deals as $deal) {
      $label = $deal->label_deals;
      if (in_array($label, $dealsLabels)) {
        $dealsByLabel[$label][] = $deal;
      }
    }

    return $this->render('index', [
      'model' => $model,
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
      'dealsLabels' => $dealsLabels,
      'dealsByLabel' => $dealsByLabel,
    ]);
  }

  public function actionView($deals_id)
  {
    return $this->renderAjax('view', [
      'model' => $this->findModel($deals_id), // findModel di bawah akan digunakan
    ]);
  }

  public function actionViewModal($id)
  {
    // Bungkus semua logika dengan try-catch
    try {
      $model = $this->findModel($id);
      $customers = Customer::find()->all();
      $products = Product::find()->all();
      $sentQuotations = Quotation::find()
        ->where(['quotation_status' => 'Sent'])
        ->orderBy(['created_at' => SORT_DESC])
        ->all();
      $quotationList = ArrayHelper::map($sentQuotations, 'quotation_id', 'quotation_code');

      return $this->renderAjax('_form', [
        'model' => $model,
        'customers' => $customers,
        'products' => $products,
        'quotationList' => $quotationList,
        'mode' => 'view',
      ]);
    } catch (\Throwable $e) {
      // Jika terjadi error, kirim pesan error sebagai response
      Yii::$app->response->format = Response::FORMAT_JSON;
      return ['error' => true, 'message' => $e->getMessage(), 'trace' => $e->getTraceAsString()];
    }
  }

  public function actionCreate()
  {
    $model = new Deals();

    // === Generate Kode Otomatis ===
    $lastDeal = Deals::find()
      ->where(['like', 'deals_code', 'DEA-%', false])
      ->andWhere(['deleted_at' => null])
      ->orderBy(['deals_id' => SORT_DESC])
      ->one();

    if ($lastDeal && preg_match('/DEA-(\d+)/', $lastDeal->deals_code, $matches)) {
      $lastNumber = (int) $matches[1];
      $newNumber = $lastNumber + 1;
      $model->deals_code = 'DEA-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    } else {
      $model->deals_code = 'DEA-001';
    }

    // === Lanjut proses form submission ===
    if (Yii::$app->request->isPost) {
      if ($model->load(Yii::$app->request->post())) {
        // Harga default jika label readonly
        // if (in_array($model->label_deals, ['New', 'Proposal Sent'])) {
        //   $product = Product::findOne($model->product_id);
        //   if ($product) {
        //     $model->price_product = $product->harga;
        //   }
        // }

        // Jika saat membuat deal baru, labelnya langsung 'Deal Won'
        if ($model->label_deals === 'Deal Won' && empty($model->purchase_date)) {
          $model->purchase_date = date('Y-m-d');
        }

        if ($model->save()) {
          if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return ['status' => 'success', 'message' => 'Data berhasil ditambahkan'];
          }
          return $this->redirect(['index']);
        } else {
          // Jika gagal menyimpan, kembalikan error JSON
          if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return [
              'status' => 'error',
              'message' => 'Data gagal disimpan.',
              'errors' => $model->getErrors(),
            ];
          }
        }
      } else {
        // Jika gagal load data dari POST, tetap kembalikan JSON
        if (Yii::$app->request->isAjax) {
          Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
          return [
            'status' => 'error',
            'message' => 'Gagal memuat data dari form POST.',
          ];
        }
      }
    }

    // Load data customers dan products untuk form
    $customers = Customer::find()->all();
    $products = Product::find()->all();

    $sentQuotations = Quotation::find()
      ->where(['quotation_status' => 'Sent']) // Filter berdasarkan status
      ->orderBy(['created_at' => SORT_DESC])   // Urutkan dari yang terbaru
      ->all();

    $quotationList = ArrayHelper::map($sentQuotations, 'quotation_id', 'quotation_code');

    if (Yii::$app->request->isAjax) {
      return $this->renderAjax('_form', [
        'model' => $model,
        'customers' => $customers,
        'products' => $products,
        'quotationList' => $quotationList,  
      ]);
    }

    return $this->render('create', [
      'model' => $model,
      'customers' => $customers,
      'products' => $products,
    ]);
  }

  public function actionUpdate($id)
  {
    // Bungkus semua logika dengan try-catch
    try {
      $model = $this->findModel($id);

      if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {

        // === LOGIKA JARING PENGAMAN ===
        if ($model->isAttributeChanged('label_deals') && $model->label_deals === 'Deal Won' && empty($model->purchase_date)) {
          $model->purchase_date = date('Y-m-d');
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if ($model->save()) {
          return ['status' => 'success', 'message' => 'Berhasil Mengubah Data'];
        } else {
          return ['status' => 'failed', 'message' => 'Gagal Mengubah Data', 'errors' => $model->getErrors()];
        }
      }

      $customers = Customer::find()->all();
      $products = Product::find()->all();
      $sentQuotations = Quotation::find()
        ->where(['quotation_status' => 'Sent'])
        ->orderBy(['created_at' => SORT_DESC])
        ->all();
      $quotationList = ArrayHelper::map($sentQuotations, 'quotation_id', 'quotation_code');

      return $this->renderAjax('_form', [
        'model' => $model,
        'customers' => $customers,
        'products' => $products,
        'quotationList' => $quotationList,
        'mode' => 'edit', // Mode 'edit' untuk update
      ]);
    } catch (\Throwable $e) {
      // Jika terjadi error, kirim pesan error sebagai response
      Yii::$app->response->format = Response::FORMAT_JSON;
      return ['error' => true, 'message' => $e->getMessage(), 'trace' => $e->getTraceAsString()];
    }
  }

  public function actionDelete($id)
  {
    $model = $this->findModel($id);
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

  protected function findModel($id)
  {
    return Deals::find()
      ->where(['deals_id' => $id])
      ->with(['customer', 'product', 'dealsHistories', 'quotations', 'activeQuotation'])
      ->one() ?? throw new NotFoundHttpException('The requested page does not exist.');
  }


  public function actionGetCustomerDetails($j)
  {
    Yii::$app->response->format = Response::FORMAT_JSON;
    $customer = Customer::find()->all();
    foreach ($customer as $i) {
      $result[] = ['id' => $i->customer_id, 'text' => $i->customer_name, 'email' => $i->customer_email];
    }
    return $result;
  }

  public function actionGetInfo($id)
  {
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    $customer = Customer::findOne($id);
    return [
      'email' => $customer->customer_email,
    ];
  }
  // public function getCustomer()
  // {
  //   return $this->hasOne(Customer::class, ['customer_id' => 'customer_id']);
  // }

  public function actionGetProductInfo($id)
  {
    Yii::$app->response->format = Response::FORMAT_JSON;
    $product = Product::findOne($id);

    if ($product) {
      return [
        'unit' => $product->unit,
        'price' => $product->harga, // Sesuaikan jika nama kolom harga berbeda
      ];
    }

    return ['unit' => '', 'price' => ''];
  }
  // public function getProduct()
  // {
  //   return $this->hasOne(Product::class, ['id_produk' => 'product_id']);
  // }

  public function actionGetQuotationDetails($id)
  {
    Yii::$app->response->format = Response::FORMAT_JSON;
    $quotation = Quotation::findOne($id);

    if ($quotation) {
      // Mengembalikan data dari Order Lines (produk utama) Quotation.
      // Pastikan nama atribut (product_id, unit_product, price_product) sesuai dengan model Quotation Anda.
      return [
        'status' => 'success',
        'product_id' => $quotation->product_id,
        'unit' => $quotation->unit_product,
        'price' => $quotation->price_product,
      ];
    }

    return ['status' => 'error', 'message' => 'Quotation not found.'];
  }
}
