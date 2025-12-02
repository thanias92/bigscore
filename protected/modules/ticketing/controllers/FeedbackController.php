<?php

namespace app\modules\ticketing\controllers;

use app\models\Deals;
use app\models\Feedback;
use app\models\Product;
use app\modules\ticketing\FeedbackSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * FeedbackController implements the CRUD actions for Feedback model.
 */
class FeedbackController extends Controller
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
          ],
        ],
      ]
    );
  }

  /**
   * Lists all Feedback models.
   * @return string
   */
  public function actionIndex()
  {
    $searchModel = new FeedbackSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    return $this->render('index', [
      // 'model' => new Feedback(),
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ]);
  }


  /**
   * Displays a single Feedback model.
   * @param int $id_feedback
   * @return string
   * @throws NotFoundHttpException
   */
  public function actionView($id_feedback)
  {
    $model = $this->findModel($id_feedback);
    return $this->renderPartial('view', [
      'model' => $model,
    ]);
  }

  /**
   * Creates a new Feedback model.
   * @return string|\yii\web\Response
   */
  public function actionCreate()
  {
    $model = new Feedback();

    if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
      Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
      if ($model->save()) {
        return [
          'status' => 'success',
          'message' => 'Berhasil Menambah Data',
        ];
      } else {
        return [
          'status' => 'failed',
          'message' => 'Gagal Menambah Data: ' . json_encode($model->errors),
        ];
      }
    }

    $deals = Deals::find()->with(['customer', 'product'])->all();

    return $this->renderAjax('_form', [
      'model' => $model,
      'deals' => $deals,
    ]);
  }

  /**
   * Updates an existing Feedback model.
   * @param int $id_feedback
   * @return string|\yii\web\Response
   * @throws NotFoundHttpException
   */
  public function actionUpdate($id_feedback)
  {
    $model = $this->findModel($id_feedback);

    if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
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

    $deals = Deals::find()->with(['customer', 'product'])->all();

    return $this->renderAjax('update', [
      'model' => $model,
      'deals' => $deals,
    ]);
  }

  /**
   * Deletes an existing Feedback model.
   * @param int $id_feedback
   * @return \yii\web\Response
   * @throws NotFoundHttpException
   */
  public function actionDelete($id_feedback)
  {
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    $model = $this->findModel($id_feedback);

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
   * Finds the Feedback model.
   * @param int $id_feedback
   * @return Feedback
   * @throws NotFoundHttpException
   */
  protected function findModel($id_feedback)
  {
    if (($model = Feedback::findOne(['id_feedback' => $id_feedback])) !== null) {
      return $model;
    }

    throw new NotFoundHttpException('Halaman yang diminta tidak ditemukan.');
  }
}