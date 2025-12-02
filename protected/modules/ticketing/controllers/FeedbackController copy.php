<?php

namespace app\modules\ticketing\controllers;

use Yii;
use app\models\Feedback;
  use app\modules\ticketing\FeedbackSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
* FeedbackController implements the CRUD actions for Feedback model.
*/
class FeedbackController extends Controller
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
* Lists all Feedback models.
*
* @return string
*/
public function actionIndex()
{
  $searchModel = new FeedbackSearch();
  $dataProvider = $searchModel->search($this->request->queryParams);

  return $this->render('index', [
  'searchModel' => $searchModel,
  'dataProvider' => $dataProvider,
  ]);
}

/**
* Displays a single Feedback model.
* @param int $id_feedback Id Feedback
* @return string
* @throws NotFoundHttpException if the model cannot be found
*/
public function actionView($id_feedback)
{
return $this->renderAjax('view', [
'model' => $this->findModel($id_feedback),
]);
}

/**
* Creates a new Feedback model.
* If creation is successful, the browser will be redirected to the 'view' page.
* @return string|\yii\web\Response
*/
public function actionCreate()
{
$model = new Feedback();

if ($this->request->isPost) {
if ($model->load($this->request->post())) {
Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
if($model->save()){
return [
'status' => 'success',
'message' => 'Berhasil Menambah Data '.(!empty($model->errors) && json_encode($model->errors))
];
} else {
return [
'status' => 'failed',
'message' => 'Gagal Menambah Data '.(!empty($model->errors) && json_encode($model->errors))
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
* Updates an existing Feedback model.
* If update is successful, the browser will be redirected to the 'view' page.
* @param int $id_feedback Id Feedback
* @return string|\yii\web\Response
* @throws NotFoundHttpException if the model cannot be found
*/
public function actionUpdate($id_feedback)
{
$model = $this->findModel($id_feedback);

if ($this->request->isPost && $model->load($this->request->post())) {
if ($model->load($this->request->post())) {
Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
if($model->save()){
return [
'status' => 'success',
'message' => 'Berhasil Mengubah Data '.(!empty($model->errors) && json_encode($model->errors))
];
} else {
return [
'status' => 'failed',
'message' => 'Gagal Mengubah Data '.(!empty($model->errors) && json_encode($model->errors))
];
}
}
}

return $this->renderAjax('update', [
'model' => $model,
]);
}

/**
* Deletes an existing Feedback model.
* If deletion is successful, the browser will be redirected to the 'index' page.
* @param int $id_feedback Id Feedback
* @return \yii\web\Response
* @throws NotFoundHttpException if the model cannot be found
*/
public function actionDelete($id_feedback)
{
$model = $this->findModel($id_feedback);
Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
if($model->delete()){
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
* Finds the Feedback model based on its primary key value.
* If the model is not found, a 404 HTTP exception will be thrown.
* @param int $id_feedback Id Feedback
* @return Feedback the loaded model
* @throws NotFoundHttpException if the model cannot be found
*/
protected function findModel($id_feedback)
{
if (($model = Feedback::findOne(['id_feedback' => $id_feedback])) !== null) {
return $model;
}

throw new NotFoundHttpException('The requested page does not exist.');
}
}
