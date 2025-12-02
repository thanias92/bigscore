<?php

namespace app\modules\vendorfinance\controllers;

use Yii;
use app\models\Product;
use app\modules\vendorfinance\ProductSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ProductController implements the CRUD actions for Product model.
 */
class ProductController extends Controller
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
   * Lists all Product models.
   *
   * @return string
   */
  public function actionIndex()
  {
    $searchModel = new ProductSearch();
    $dataProvider = $searchModel->search($this->request->queryParams);

    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ]);
  }

  /**
   * Displays a single Product model.
   * @param int $id_produk Id Produk
   * @return string
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionView($id_produk)
  {
    return $this->renderAjax('view', [
      'model' => $this->findModel($id_produk),
    ]);
  }

  /**
   * Creates a new Product model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   * @return string|\yii\web\Response
   */
  public function actionCreate()
  {
    $model = new Product();
    $lastProduk = Product::find()
      ->where(['like', 'code_produk', 'PRO-%', false])
      ->andWhere(['deleted_at' => null]) // jika pakai soft delete
      ->orderBy(['id_produk' => SORT_DESC]) // GANTI 'product_id' sesuai kolom yang ada
      ->one();

    if ($lastProduk && preg_match('/PRO-(\d+)/', $lastProduk->code_produk, $matches)) {
      $lastNumber = (int)$matches[1];
      $newNumber = $lastNumber + 1;
      $model->code_produk = 'PRO-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    } else {
      $model->code_produk = 'PRO-001';
    }
    $model->no_produk = $model->code_produk;
    if ($this->request->isPost) {
      if ($model->load($this->request->post())) {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if ($model->save()) {
          return [
            'status' => 'success',
            'message' => 'Berhasil Menambah Data ' . (!empty($model->errors) && json_encode($model->errors))
          ];
        } else {
          return [
            'status' => 'failed',
            'message' => 'Gagal Menambah Data ' . (!empty($model->errors) && json_encode($model->errors))
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
   * Updates an existing Product model.
   * If update is successful, the browser will be redirected to the 'view' page.
   * @param int $id_produk Id Produk
   * @return string|\yii\web\Response
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionUpdate($id_produk)
  {
    $model = $this->findModel($id_produk);

    if ($this->request->isPost && $model->load($this->request->post())) {
      if ($model->load($this->request->post())) {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if ($model->save()) {
          return [
            'status' => 'success',
            'message' => 'Berhasil Mengubah Data ' . (!empty($model->errors) && json_encode($model->errors))
          ];
        } else {
          return [
            'status' => 'failed',
            'message' => 'Gagal Mengubah Data ' . (!empty($model->errors) && json_encode($model->errors))
          ];
        }
      }
    }

    return $this->renderAjax('update', [
      'model' => $model,
    ]);
  }

  /**
   * Deletes an existing Product model.
   * If deletion is successful, the browser will be redirected to the 'index' page.
   * @param int $id_produk Id Produk
   * @return \yii\web\Response
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionDelete($id_produk)
  {
    $model = $this->findModel($id_produk);
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

  /**
   * Finds the Product model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param int $id_produk Id Produk
   * @return Product the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id_produk)
  {
    if (($model = Product::findOne(['id_produk' => $id_produk])) !== null) {
      return $model;
    }

    throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
  }
}
