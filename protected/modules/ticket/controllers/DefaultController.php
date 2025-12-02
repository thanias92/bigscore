<?php

namespace app\modules\v1\controllers;

use app\models\Karyawan;
use Yii;
use yii\web\Response;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use app\models\User;
use app\models\LogLogin;
use sizeg\jwt\JwtHttpBearerAuth;

class AuthController extends \yii\rest\Controller
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => JwtHttpBearerAuth::class,
        ];
        $behaviors['authenticator']['except'] = ['login'];

        return $behaviors;
    }

    public function actionLogin()
    {
      $jsonPayload = Yii::$app->request->getRawBody();
      $data = json_decode($jsonPayload, true);
      $email = $data['email'];
      $password = $data['password'];

      $user = User::findByEmail($email);

      if ($user && $user->validatePassword($password)) {
        $signer = new Sha256();
        $expire = time() + \Yii::$app->params['JwtExpire'];
        $jwt = \Yii::$app->jwt;
        $token = $jwt->getBuilder()
            ->setIssuer(\Yii::$app->params['JwtIssuer']) // Configures the issuer (iss claim)
            ->setAudience(\Yii::$app->params['JwtAudience']) // Configures the audience (aud claim)
            ->setId(\Yii::$app->params['TokenID'], true) // Configures the id (jti claim), replicating as a header item
            ->setIssuedAt(time()) // Configures the time that the token was issue (iat claim)
            ->setExpiration($expire) // Configures the expiration time of the token (exp claim)
            ->set('uid', $user->id) // Configures a new claim, called "uid"
            ->sign($signer, $jwt->key) // creates a signature using [[Jwt::$key]]
            ->getToken(); // Retrieves the generated token


        return [
          'email' => $user->email,
          'token' => (string)$token,
        ];
      } else {
        Yii::$app->response->statusCode = 401;
        return [
          'error' => 'Invalid username or password.',
        ];
      }
    }
}
