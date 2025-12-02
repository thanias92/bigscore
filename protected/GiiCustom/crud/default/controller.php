<?php

/**
 * This is the template for generating a CRUD controller class file.
 */

use yii\db\ActiveRecordInterface;
use yii\helpers\StringHelper;


/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$controllerClass = StringHelper::basename($generator->controllerClass);
$modelClass = StringHelper::basename($generator->modelClass);
$searchModelClass = StringHelper::basename($generator->searchModelClass);
if ($modelClass === $searchModelClass) {
  $searchModelAlias = $searchModelClass . 'Search';
}

/* @var $class ActiveRecordInterface */
$class = $generator->modelClass;
$pks = $class::primaryKey();
$urlParams = $generator->generateUrlParams();
$actionParams = $generator->generateActionParams();
$actionParamComments = $generator->generateActionParamComments();

echo "<?php\n";
?>

namespace <?= StringHelper::dirname(ltrim($generator->controllerClass, '\\')) ?>;

use Yii;
use <?= ltrim($generator->modelClass, '\\') ?>;
<?php if (!empty($generator->searchModelClass)) : ?>
  use <?= ltrim($generator->searchModelClass, '\\') . (isset($searchModelAlias) ? " as $searchModelAlias" : "") ?>;
<?php else : ?>
  use yii\data\ActiveDataProvider;
<?php endif; ?>
use <?= ltrim($generator->baseControllerClass, '\\') ?>;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
* <?= $controllerClass ?> implements the CRUD actions for <?= $modelClass ?> model.
*/
class <?= $controllerClass ?> extends <?= StringHelper::basename($generator->baseControllerClass) . "\n" ?>
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
* Lists all <?= $modelClass ?> models.
*
* @return string
*/
public function actionIndex()
{
<?php if (!empty($generator->searchModelClass)) : ?>
  $searchModel = new <?= isset($searchModelAlias) ? $searchModelAlias : $searchModelClass ?>();
  $dataProvider = $searchModel->search($this->request->queryParams);

  return $this->render('index', [
  'searchModel' => $searchModel,
  'dataProvider' => $dataProvider,
  ]);
<?php else : ?>
  $dataProvider = new ActiveDataProvider([
  'query' => <?= $modelClass ?>::find(),
  /*
  'pagination' => [
  'pageSize' => 50
  ],
  'sort' => [
  'defaultOrder' => [
  <?php foreach ($pks as $pk) : ?>
    <?= "'$pk' => SORT_DESC,\n" ?>
  <?php endforeach; ?>
  ]
  ],
  */
  ]);

  return $this->render('index', [
  'dataProvider' => $dataProvider,
  ]);
<?php endif; ?>
}

/**
* Displays a single <?= $modelClass ?> model.
* <?= implode("\n     * ", $actionParamComments) . "\n" ?>
* @return string
* @throws NotFoundHttpException if the model cannot be found
*/
public function actionView(<?= $actionParams ?>)
{
return $this->renderAjax('view', [
'model' => $this->findModel(<?= $actionParams ?>),
]);
}

/**
* Creates a new <?= $modelClass ?> model.
* If creation is successful, the browser will be redirected to the 'view' page.
* @return string|\yii\web\Response
*/
public function actionCreate()
{
$model = new <?= $modelClass ?>();

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
* Updates an existing <?= $modelClass ?> model.
* If update is successful, the browser will be redirected to the 'view' page.
* <?= implode("\n     * ", $actionParamComments) . "\n" ?>
* @return string|\yii\web\Response
* @throws NotFoundHttpException if the model cannot be found
*/
public function actionUpdate(<?= $actionParams ?>)
{
$model = $this->findModel(<?= $actionParams ?>);

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
* Deletes an existing <?= $modelClass ?> model.
* If deletion is successful, the browser will be redirected to the 'index' page.
* <?= implode("\n     * ", $actionParamComments) . "\n" ?>
* @return \yii\web\Response
* @throws NotFoundHttpException if the model cannot be found
*/
public function actionDelete(<?= $actionParams ?>)
{
$model = $this->findModel(<?= $actionParams ?>);
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
* Finds the <?= $modelClass ?> model based on its primary key value.
* If the model is not found, a 404 HTTP exception will be thrown.
* <?= implode("\n     * ", $actionParamComments) . "\n" ?>
* @return <?= $modelClass ?> the loaded model
* @throws NotFoundHttpException if the model cannot be found
*/
protected function findModel(<?= $actionParams ?>)
{
<?php
$condition = [];
foreach ($pks as $pk) {
  $condition[] = "'$pk' => \$$pk";
}
$condition = '[' . implode(', ', $condition) . ']';
?>
if (($model = <?= $modelClass ?>::findOne(<?= $condition ?>)) !== null) {
return $model;
}

throw new NotFoundHttpException(<?= $generator->generateString('The requested page does not exist.') ?>);
}
}
