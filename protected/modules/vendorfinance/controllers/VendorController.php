<?php

namespace app\modules\vendorfinance\controllers;

use Yii;
use app\models\Vendor;
use app\modules\vendorfinance\VendorSearch;
use yii\data\ActiveDataProvider;
use app\models\Staff;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * VendorController implements the CRUD actions for Vendor model.
 */
class VendorController extends Controller
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
   * Lists all Vendor models.
   *
   * @return string
   */
  public function actionIndex($filter = 'vendor')
  {
    if ($filter === 'staff') {
      $searchModel = new \app\modules\vendorfinance\StaffSearch(); // Pastikan StaffSearch tersedia
      $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    } else {
      $searchModel = new \app\modules\vendorfinance\VendorSearch();
      $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    }

    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
      'filter' => $filter,
    ]);
  }

  /**
   * Digunakan untuk pencarian data vendor via AJAX (misal untuk Select2)
   * @param string|null $q
   * @return array
   */
  public function actionSearchVendor($q = null)
  {
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    return Vendor::getDataList($q);
  }

  /**
   * Digunakan untuk pencarian vendor atau staff dengan query string (misalnya untuk Select2 AJAX)
   * @param string|null $q Kata kunci pencarian
   * @param string $filter Jenis data yang dicari: vendor (default) atau staff
   * @return array
   */
  public function actionSearch($q = null, $filter = 'vendor')
  {
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

    if ($filter === 'staff') {
      return \app\models\Staff::getDataList($q);
    }

    // Default: vendor
    return \app\models\Vendor::getDataList($q);
  }

  /**
   * Creates a new Vendor model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   * @return string|\yii\web\Response
   */
  public function actionCreate()
  {
    $model = new Vendor();

    if ($this->request->isPost) {
      if ($model->load($this->request->post())) {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if ($model->save()) {
          return [
            'status' => 'success',
            'message' => 'Berhasil Menambah Data ' . json_encode($model->errors),
          ];
        } else {
          return [
            'status' => 'failed',
            'message' => 'Gagal Menambah Data ' . json_encode($model->errors),
          ];
        }
      }
    } else {
      $model->loadDefaultValues();
    }

    return $this->renderAjax('create', [
      'model' => $model,
    ]);
  }

  /**
   * Updates an existing Vendor model.
   * If update is successful, the browser will be redirected to the 'view' page.
   * @param int $id_vendor Id Vendor
   * @return string|\yii\web\Response
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionUpdate($id_vendor)
  {
    $model = $this->findModel($id_vendor);

    if ($this->request->isPost && $model->load($this->request->post())) {
      Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
      if ($model->save()) {
        return [
          'status' => 'success',
          'message' => 'Berhasil Mengubah Data',
        ];
      } else {
        return [
          'status' => 'failed',
          'message' => 'Gagal Mengubah Data: ' . json_encode($model->errors),
        ];
      }
    }

    return $this->renderAjax('update', [
      'model' => $model,
    ]);
  }

  public function actionView($id_vendor)
  {
    $this->layout = false; 
    return $this->render('view', [
      'model' => $this->findModel($id_vendor),
  ]);
  }
  /**
   * Deletes an existing Vendor model.
   * If deletion is successful, the browser will be redirected to the 'index' page.
   * @param int $id_vendor Id Vendor
   * @return \yii\web\Response
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionDelete($id_vendor)
  {
    $model = $this->findModel($id_vendor);
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    $model->deleted_by = Yii::$app->user->id;
    $model->deleted_at = date('Y-m-d H:i:s');

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

  /**
   * Finds the Vendor model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param int $id_vendor Id Vendor
   * @return Vendor the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id_vendor)
  {
    if (($model = Vendor::findOne(['id_vendor' => $id_vendor])) !== null) {
      return $model;
    }

    throw new NotFoundHttpException('The requested page does not exist.');
  }
}
