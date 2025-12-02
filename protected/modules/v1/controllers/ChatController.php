<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\Response;
use app\models\Tenant;
use sizeg\jwt\JwtHttpBearerAuth;
use app\models\Roomchat;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\auth\HttpBearerAuth;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Builder; // untuk versi 3.x
use app\models\User;
use app\models\LoginForm;
use yii\helpers\Html;
use app\components\JwtHelper;
use yii\filters\ContentNegotiator;

class ChatController extends Controller
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

    public function actionTambahNew()
    {
        $jsonPayload = Yii::$app->request->getRawBody();
        $data = json_decode($jsonPayload, true);
        $id_customer = $data['id_customer'];
        $message = $data['message'];
        $exiting_roomchat = Roomchat::find()->where(['id_customer' => $id_customer])->one();
        // if ($exiting_roomchat) {
        //     $exiting_roomchat->id_customer = $id_customer;
        //     $exiting_roomchat->chat = $message;
        //     $exiting_roomchat->send_at = date('Y-m-d H:i:s');
        //     $exiting_roomchat->is_read = 0;
        // } else {
        $exiting_roomchat = new Roomchat();
        $exiting_roomchat->id_customer = $id_customer;
        $exiting_roomchat->chat = $message;
        $exiting_roomchat->send_at = date('Y-m-d H:i:s');
        $exiting_roomchat->is_read = 0;
        // }
        if ($exiting_roomchat->save()) {
            $response =
                [
                    'response' => $message,
                    'metadata' => [
                        'code' => 200,
                        'message' => "Pesan Berhasil Di Kirim"
                    ]
                ];
        } else {
            $response =
                [
                    'response' => $message,
                    'metadata' => [
                        'code' => 500,
                        'message' => "Terjadi kesalahan dalam memproses permintaan."
                    ]
                ];
        }
        return $response;
    }
    public function actionLoadChatJson($id_customer)
    {
        Yii::error("Masuk ke LoadChatJson: $id_customer", 'debug');
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $chatMessages = Roomchat::find()
            ->where(['id_customer' => $id_customer])
            ->orderBy(['send_at' => SORT_ASC])
            ->all();

        $chatData = [];

        foreach ($chatMessages as $chat) {
            $isStaff = $chat->id_staff !== null;

            $chatData[] = [
                'sender_type' => $isStaff ? 'staff' : 'customer',
                'sender_name' => $isStaff
                    ? ($chat->staff->nama_staff ?? 'Staff')
                    : ($chat->customer->PIC_name ?? 'Customer'),
                'message' => $chat->chat,
                'send_at' => Yii::$app->formatter->asDatetime($chat->send_at),
            ];
        }
        $response =
            [
                'response' => [
                    'data' => $chatData
                ],
                'metadata' => [
                    'code' => 200,
                    'message' => "success"
                ]
            ];
        return $response;
    }
}
