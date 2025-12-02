<?php

namespace app\controllers;

use Yii;
use app\models\Tenant;
use app\models\TenantSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
* TenantController implements the CRUD actions for Tenant model.
*/
class TenantController extends Controller
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
    * Lists all Tenant models.
    *
    * @return string
    */
    public function actionIndex()
    {
        $searchModel = new TenantSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
        ]);
    }

    /**
    * Displays a single Tenant model.
    * @param int $id ID
    * @return string
    * @throws NotFoundHttpException if the model cannot be found
    */
    public function actionView($id)
    {
        return $this->renderAjax('view', [
        'model' => $this->findModel($id),
        ]);
    }

    /**
    * Creates a new Tenant model.
    * If creation is successful, the browser will be redirected to the 'view' page.
    * @return string|\yii\web\Response
    */
    public function actionCreate()
    {
        $model = new Tenant();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                $model->uuid = \app\components\Uuid::guidv4();
                $model->created_at = time();
                $model->created_by = Yii::$app->user->id;
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                if($model->save()) {
                    return [
                    'status' => 'success',
                    'message' => 'Berhasil Menambah Data '.(!empty($model->errors) && json_encode($model->errors))
                    ];
                } else {
                    return [
                    'status' => 'failed',
                    'message' => 'Gagal Menambah Data '.json_encode($model->errors)
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
    * Updates an existing Tenant model.
    * If update is successful, the browser will be redirected to the 'view' page.
    * @param int $id ID
    * @return string|\yii\web\Response
    * @throws NotFoundHttpException if the model cannot be found
    */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post())) {
            if ($model->load($this->request->post())) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                if($model->save()) {
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
    * Deletes an existing Tenant model.
    * If deletion is successful, the browser will be redirected to the 'index' page.
    * @param int $id ID
    * @return \yii\web\Response
    * @throws NotFoundHttpException if the model cannot be found
    */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if($model->delete()) {
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
    * Finds the Tenant model based on its primary key value.
    * If the model is not found, a 404 HTTP exception will be thrown.
    * @param int $id ID
    * @return Tenant the loaded model
    * @throws NotFoundHttpException if the model cannot be found
    */
    protected function findModel($id)
    {
        if (($model = Tenant::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
