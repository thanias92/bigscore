<?php

namespace app\modules\sales\controllers;

use Yii;
use app\models\Customer;
use app\models\CustomerVisit;
use app\modules\sales\CustomerSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

class CustomerController extends Controller
{
  /**
   * @inheritdoc
   */
  public function behaviors()
  {
    return [
      'verbs' => [
        'class' => VerbFilter::class,
        'actions' => [
          'delete' => ['POST'],
        ],
      ],
    ];
  }


  public function actionIndex()
  {
    $searchModel = new CustomerSearch();
    $dataProvider = $searchModel->search($this->request->queryParams);

    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ]);
  }

  public function actionViewModal($customer_id)
  {
    return $this->renderAjax('_form', [
      'model' => $this->findModel($customer_id),
      'mode' => 'view',
    ]);
  }

  public function actionCreate()
  {
    $model = new Customer();

    if ($model->isNewRecord) {
      $model->establishment_date = date('Y-m-d');
    }

    // Generate customer_code
    $lastCustomer = Customer::find()
      ->where(['like', 'customer_code', 'CUS-%', false])
      ->andWhere(['deleted_at' => null])
      ->orderBy(['customer_id' => SORT_DESC])
      ->one();

    if ($lastCustomer && preg_match('/CUS-(\d+)/', $lastCustomer->customer_code, $matches)) {
      $lastNumber = (int)$matches[1];
      $newNumber = $lastNumber + 1;
      $model->customer_code = 'CUS-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    } else {
      $model->customer_code = 'CUS-001';
    }

    if ($this->request->isPost) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      if ($model->load($this->request->post()) && $model->save()) {
        return ['success' => true, 'message' => 'Customer berhasil ditambahkan.'];
      }
      // Jika gagal, render kembali form dengan pesan error untuk ditampilkan di modal
      return ['success' => false, 'content' => $this->renderAjax('_form', ['model' => $model, 'mode' => 'create'])];
    }

    return $this->renderAjax('_form', [
      'model' => $model,
      'mode' => 'create',
    ]);
  }

  public function actionUpdate($customer_id)
  {
    $model = $this->findModel($customer_id);

    if ($this->request->isPost) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      if ($model->load($this->request->post()) && $model->save()) {
        return ['success' => true, 'message' => 'Customer berhasil diperbarui.'];
      }
      return ['success' => false, 'content' => $this->renderAjax('_form', ['model' => $model, 'mode' => 'edit'])];
    }

    return $this->renderAjax('_form', [
      'model' => $model,
      'mode' => 'edit',
    ]);
  }

  public function actionDelete($customer_id)
  {
    Yii::$app->response->format = Response::FORMAT_JSON;
    if ($this->findModel($customer_id)->delete()) {
      return ['success' => true, 'message' => 'Data berhasil dihapus.'];
    }
    return ['success' => false, 'message' => 'Gagal menghapus data.'];
  }

  protected function findModel($customer_id)
  {
    if (($model = Customer::find()->where(['customer_id' => $customer_id])->with('customerHistories', 'customerVisits')->one()) !== null) {
      return $model;
    }

    throw new NotFoundHttpException('The requested page does not exist.');
  }

  public function actionLogVisit($customer_id)
  {
    $model = new CustomerVisit();
    $customer = $this->findModel($customer_id); // Pastikan customer ada

    $model->customer_id = $customer->customer_id;
    $model->visit_date = date('Y-m-d'); // Otomatis isi tanggal hari ini

    // Jika form di-submit (method POST)
    if ($model->load(Yii::$app->request->post()) && $model->save()) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      return ['success' => true, 'message' => 'Catatan kunjungan berhasil disimpan.'];
    }

    // Jika hanya menampilkan form (method GET)
    return $this->renderAjax('_visit_form', [
      'model' => $model,
    ]);
  }
}
