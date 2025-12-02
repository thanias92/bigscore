<?php

namespace app\modules\task\controllers;

use Yii;
use app\models\Ticket;
use app\modules\task\TicketSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TicketController implements the CRUD actions for Ticket model.
 */
class TicketController extends Controller
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
   * Lists all Ticket models.
   *
   * @return string
   */
  public function actionIndex()
  {
    $searchModel = new TicketSearch();
    $dataProvider = $searchModel->search($this->request->queryParams);

    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ]);
  }

  /**
   * Displays a single Ticket model.
   * @param int $id_ticket Id Ticket
   * @return string
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionView($id_ticket)
  {
    return $this->renderAjax('view', [
      'model' => $this->findModel($id_ticket),
    ]);
  }

  /**
   * Creates a new Ticket model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   * @return string|\yii\web\Response
   */
  public function actionCreate()
  {
    $model = new Ticket();

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
   * Updates an existing Ticket model.
   * If update is successful, the browser will be redirected to the 'view' page.
   * @param int $id_ticket Id Ticket
   * @return string|\yii\web\Response
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionUpdate($id_ticket)
  {
    $model = $this->findModel($id_ticket);

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
   * Deletes an existing Ticket model.
   * If deletion is successful, the browser will be redirected to the 'index' page.
   * @param int $id_ticket Id Ticket
   * @return \yii\web\Response
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionDelete($id_ticket)
  {
    $model = $this->findModel($id_ticket);
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
   * Finds the Ticket model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param int $id_ticket Id Ticket
   * @return Ticket the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id_ticket)
  {
    if (($model = Ticket::findOne(['id_ticket' => $id_ticket])) !== null) {
      return $model;
    }

    throw new NotFoundHttpException('The requested page does not exist.');
  }
}
