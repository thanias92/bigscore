<?php

namespace app\modules\vendorfinance\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\Response;
use app\models\Tenant;
use sizeg\jwt\JwtHttpBearerAuth;

class TenantController extends Controller
{
    public $modelClass = "app\models\Tenant";
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;
        $behaviors['authenticator'] = [
            'class' => JwtHttpBearerAuth::class,
        ];

        return $behaviors;
    }

    public function actionView($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $tenant = Tenant::find()->select(['host', 'code', 'name', 'uuid'])->where(['code' => $id, 'status' => true])->one();
            if (!$tenant) {
                return [
                    'ret' => 404,
                    'data' => null,
                    'msg' => "Temamt dengan ID $id tidak ditemukan.",
                ];
            }

            return [
                'ret' => 200,
                'data' => $tenant,
                'msg' => 'OK',
            ];
        } catch (\Exception $e) {
            Yii::$app->response->setStatusCode(500);
            return [
                'ret' => 500,
                'data' => null,
                'msg' => 'Terjadi kesalahan dalam memproses permintaan.',
            ];
        }
    }
}
