<?php

namespace app\modules\core\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

use app\components\MenuHelper;
use app\models\Menu;
use app\models\AuthItemPure as AuthItem;
use app\models\MenuSearch;

/**
 * MenuController implements the CRUD actions for Menu model.
 */
class MenuController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Menu models.
     * @return mixed
     */
    public function actionIndex()
    {

        $searchModel = new MenuSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
        $model = new Menu;
        if (Yii::$app->request->post()) {
            $data = json_decode($_POST['Menu']['json_tree']);
            MenuHelper::MenuJsonSave($data, $parent = null);
        }
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'model' => $model,
        ]);
    }

    /**
     * Displays a single Menu model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {

        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('view', ['model' => $model]);
        }
    }

    /**
     * Creates a new Menu model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {

        $model = new Menu;

        if ($model->load(Yii::$app->request->post())) {
            $model->parent = $model->parent == "" ? null : $model->parent;
            $model->route = $model->route == "" ? null : $model->route;
            $model->data = $model->data == "" ? null : $model->data;
            if(isset($_POST['Menu']['params'])) {
              $dataParams = [];
              foreach($_POST['Menu']['params'] as $row) {
                $dataParams[$row['key']] = $row['value'];
              }
              $model->params = Json::encode($dataParams, $asArray = true);
            } else {
              $model->params = null;
            }
            if (isset($_GET['id'])) {
                $model->parent = $_GET['id'];
            }
            $cek = Menu::find()->where(['parent' => $model->parent])->orderBy(['order' => SORT_DESC])->one();
            if (!empty($cek)) {
                $model->order = $cek->order + 1;
            } else {
                $model->order = 1;
            }
            if ($model->save()) {
                return $this->redirect(['index']);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Menu model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {

        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
          $model->parent = $model->parent == "" ? null : $model->parent;
          $model->route = $model->route == "" ? null : $model->route;
          $model->data = $model->data == "" ? null : $model->data;
          if (isset($_POST['Menu']['params'])) {
            $dataParams = [];
            foreach ($_POST['Menu']['params'] as $row) {
                $dataParams[$row['key']] = $row['value'];
            }
            $model->params = Json::encode($dataParams, $asArray = true);
          } else {
              $model->params = null;
          }
          if ($model->save()) {
              return $this->redirect(['index']);
          }
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Menu model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public static function getParent($id){
      if($id !== NULL) {
        $menu = Menu::find()->andWhere(['!=', 'id', $id])->orderBy('name ASC')->all();
      } else {
        $menu = Menu::find()->orderBy('name ASC')->all();
      }
        if($menu !== null){
          $data = [];
          foreach($menu as $row) {
            $data[$row->id] = $row->name.' | Route : '.$row->route.' | Parent :'.($row->parent == null ? 'null' : $row->parent0->name);
          }
            return $data;
        }
    }

    public static function getRouteReference(){
        $auth = AuthItem::find()->where(['like','name','/'])->andWhere(['not like', 'name', '*'])->orderBy('name ASC')->all();
        if($auth !== null){
          $data = [];
          foreach($auth as $row) {
            $data[$row->name] = $row->name;
          }
            return $data;
        }
    }
    /**
     * Finds the Menu model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Menu the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Menu::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
