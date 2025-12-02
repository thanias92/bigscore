<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\User; // Pastikan ini namespace dan nama model User BIGS core kamu
use yii\widgets\ActiveForm;
use yii\helpers\Html;

class BigsAuthController extends Controller
{
    public $layout = false; // Tidak menggunakan layout default

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['/sales/']); // Redirect setelah login berhasil
        }

        $model = new \yii\base\Model(['scenario' => 'login']);
        $model->email = '';
        $model->password = '';
        $model->rememberMe = false; // Tambahkan properti rememberMe jika kamu menggunakannya

        if ($model->load(Yii::$app->request->post()) && $this->login($model)) {
            return $this->redirect(['/sales/']);
        } else {
            $model->password = '';
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    protected function login($model)
    {
        $user = User::findByEmail($model->email); // Sesuaikan dengan cara kamu mencari user BIGS core

        if ($user && $user->validatePassword($model->password)) {
            return Yii::$app->user->login($user, $model->rememberMe ? 3600 * 24 * 30 : 0); // Durasi remember me
        } else {
            Yii::$app->session->setFlash('error', 'Email atau password salah.');
            return false;
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome(); // Redirect ke halaman home setelah logout
    }
}
