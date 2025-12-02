<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\helpers\Url;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use app\models\User;
use yii\web\NotFoundHttpException;
use sizeg\jwt\JwtHttpBearerAuth;
use app\models\Pengaturanakun;
use yii\web\UploadedFile;

class DefaultControllercopy extends Controller
{
  public function actions()
  {
    return [
      'error' => [
        'class' => 'yii\web\ErrorAction',
      ],
    ];
  }

  public function actionIndex()
  {
    return $this->render('index');
  }

  public function actionLogin()
  {
    $this->layout = "main-login";

    if (!Yii::$app->user->isGuest) {
      return $this->goHome();
    }

    $model = new LoginForm();
    if ($model->load(Yii::$app->request->post()) && $model->login()) {
      return $this->goBack();
    }

    $model->password = '';
    return $this->render('login', [
      'model' => $model,
    ]);
  }



  public function actionLogout()
  {
    if (Yii::$app->user->identity != '') {
      Yii::$app->user->logout();
    }

    return $this->goHome();
  }

  public function actionSettingAccount()
  {
    $model = Pengaturanakun::findOne(1); // hanya ambil id = 1

    if (!$model) {
      throw new NotFoundHttpException('Pengaturan akun tidak ditemukan.');
    }

    if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
      $logoFile = UploadedFile::getInstance($model, 'logoFile');
      if ($logoFile) {
        $filename = 'logo_' . time() . '.' . $logoFile->extension;
        $path = Yii::getAlias('@webroot/uploads/' . $filename);
        if ($logoFile->saveAs($path)) {
          $model->logo = '/uploads/' . $filename;
        }
      }

      $signature = Yii::$app->request->post('CompanySetting')['signatureData'] ?? null;
      if ($signature) {
        $signature = str_replace('data:image/png;base64,', '', $signature);
        $signature = base64_decode($signature);
        $sigPath = 'uploads/signature_' . time() . '.png';
        file_put_contents($sigPath, $signature);
        $model->ttd = '/' . $sigPath;
      }

      if ($model->save(false)) {
        Yii::$app->session->setFlash('success', 'Pengaturan berhasil disimpan.');
        return $this->redirect(Yii::$app->request->referrer);
      }
    }

    return $this->renderAjax('company_setting', [
      'model' => $model,
    ]);
  }
}
