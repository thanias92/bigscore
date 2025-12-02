<?php

namespace app\modules\vendorfinance\controllers;

use Yii;
use app\models\AccountKeluar;
use app\modules\vendorfinance\AccountKeluarSearch;
use yii\web\Controller;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AccountKeluarController implements the CRUD actions for AccountKeluar model.
 */
class AccountKeluarController extends Controller
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
   * Lists all AccountKeluar models.
   *
   * @return string
   */
  public function actionIndex()
  {
    $searchModel = new AccountKeluarSearch();
    $dataProvider = $searchModel->search($this->request->queryParams);

    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ]);
  }

  /**
   * Displays a single AccountKeluar model.
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
   * Creates a new AccountKeluar model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   * @return string|\yii\web\Response
   */
  public function actionCreate()
  {
    $model = new AccountKeluar();

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
   * Updates an existing AccountKeluar model.
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

  public function actionChildAkun($parent_id)
  {
    Yii::$app->response->format = \yii\web\Response::FORMAT_HTML;

    $children = AccountKeluar::find()
      ->where(['parent_id' => $parent_id])
      ->orderBy(['code' => SORT_ASC])
      ->all();

    $html = '';
    foreach ($children as $child) {
      $html .= '<tr class="child-of-' . $parent_id . '" data-key="' . $child->id . '">';

      /*  kolom‑kolom harus match Grid parent:
              1  Serial             (kosong)
              2  tombol toggle      (kosong)
              3  code
              4  akun
              5  penggunaan
              6  actions            (kanan)
        */
      $html .= '<td></td>';               // serial (blank)
      $html .= '<td></td>';               // caret   (blank)
      $html .= '<td>' . Html::encode($child->code) . '</td>';
      $html .= '<td>' . Html::encode($child->akun) . '</td>';
      $html .= '<td>' . Html::encode($child->penggunaan) . '</td>';

      // tombol aksi
      $aksi  = Html::a('<i class="fa fa-eye"></i>', '#', [
        'value' => Url::to(['view', 'id' => $child->id]),
        'class' => 'showModalButton text-primary me-1',
        'title' => 'Detail'
      ]);
      $aksi .= Html::a('<i class="fa fa-edit"></i>', '#', [
        'value' => Url::to(['update', 'id' => $child->id]),
        'class' => 'showModalButton text-success me-1',
        'title' => 'Edit'
      ]);
      $aksi .= Html::a('<i class="fa fa-trash"></i>', '#', [
        'id'    => $child->id,
        'class' => 'text-danger delete',
        'title' => 'Hapus'
      ]);

      $html .= '<td style="text-align:right">' . $aksi . '</td>';
      $html .= '</tr>';
    }

    return $html;
  }

  /**
   * Deletes an existing AccountKeluar model.
   * If deletion is successful, the browser will be redirected to the 'index' page.
   * @param int $id ID
   * @return \yii\web\Response
   * @throws NotFoundHttpException if the model cannot be found
   */
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

  /**
   * Finds the AccountKeluar model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param int $id ID
   * @return AccountKeluar the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id)
  {
    if (($model = AccountKeluar::findOne(['id' => $id])) !== null) {
      return $model;
    }

    throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
  }
}
