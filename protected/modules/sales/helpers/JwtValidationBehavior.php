<?php

namespace app\modules\sales\helpers;

use Yii;
use yii\base\Behavior;
use yii\web\Response;

class JwtValidationBehavior extends Behavior
{
  public function events()
  {
    return [
      \yii\web\Controller::EVENT_BEFORE_ACTION => 'beforeAction',
    ];
  }

  public function beforeAction($event)
  {
    $authorizationHeader = Yii::$app->request->getHeaders()->get('Authorization');

    if (!$authorizationHeader || !str_starts_with($authorizationHeader, 'Bearer ')) {
      Yii::$app->response->statusCode = 401;
      Yii::$app->response->data = ['error' => 'Invalid or missing Bearer token.'];
      Yii::$app->end();
    }

    $token = substr($authorizationHeader, 7);
    try {
      $decodedToken = Yii::$app->jwt->getParser()->parse((string)$token);
      if ($decodedToken->isExpired()) {
        Yii::$app->response->statusCode = 401;
        Yii::$app->response->data = ['error' => 'Token has expired. '];
        Yii::$app->end();
      }
      Yii::$app->controller->decodedToken = $decodedToken;
    } catch (\Exception $e) {
      Yii::$app->response->statusCode = 401;
      Yii::$app->response->data = ['error' => 'Invalid token.'];
      Yii::$app->end();
    }
  }
}
