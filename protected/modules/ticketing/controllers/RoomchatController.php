<?php

namespace app\modules\ticketing\controllers;

use Yii;
use app\models\Roomchat;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
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

use yii\web\Response;

/**
 * RoomchatController implements the CRUD actions for Roomchat model.
 */
class RoomchatController extends Controller
{
    /**
     * @inheritDoc
     */
    // public $enableCsrfValidation = false; // <- tambahkan ini
    // public function behaviors()
    // {
    //     return [
    //         'contentNegotiator' => [
    //             'class' => \yii\filters\ContentNegotiator::class,
    //             'formats' => [
    //                 'application/json' => \yii\web\Response::FORMAT_JSON,
    //             ],
    //         ],
    //         'authenticator' => [
    //             'class' => \yii\filters\auth\HttpBearerAuth::class,
    //             'except' => ['send-chat-api'],
    //         ],
    //     ];
    // }



    /**
     * POST /roomchat-api/login
     * {
     *     "email": "admin@gmail.com",
     *     "password": "12345"
     * }
     */
    public function actionLoginApi()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        Yii::error("POST Data: " . json_encode(Yii::$app->request->post()), 'debug');

        try {
            $model = new LoginForm();

            if ($model->load(Yii::$app->request->post(), '') && $model->login()) {
                $user = $model->getUser();

                $signer = new Sha256();
                $time = time();

                $token = (new Builder())
                    ->setIssuer(Yii::$app->params['JwtIssuer'])        // iss
                    ->setId(Yii::$app->params['TokenID'], true)       // jti
                    ->setIssuedAt($time)                               // iat
                    ->setExpiration($time + 3600)                      // exp
                    ->set('uid', $user->id)                            // custom claim
                    ->sign($signer, Yii::$app->params['TokenEncryptionKey']) // sign
                    ->getToken();

                return [
                    'success' => true,
                    'token' => (string)$token,
                    'user_id' => $user->id,
                    'username' => $user->username,
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Login gagal. Email atau password salah.',
                    'errors' => $model->getErrors(),
                ];
            }
        } catch (\Throwable $e) {
            Yii::error($e->getMessage(), 'login');
            Yii::error($e->getTraceAsString(), 'login');

            return [
                'success' => false,
                'message' => 'Terjadi error: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ];
        }
    }

    // public function actionTesHash()
    // {
    //     $password = '12345';
    //     $hash = '$2y$13$cx9UWqK2ipNUr9BKUI1X1.KN2B8z88SBlZR6nXxhUpUp96ymAaoW';
    //     $valid = Yii::$app->security->validatePassword($password, $hash);
    //     return $valid ? 'MATCH' : 'NO MATCH';
    // }

    // public function behaviors()
    // {
    //     $behaviors = parent::behaviors();

    //     // Kalau pakai AccessControl (dari mdm\admin misalnya)
    //     $behaviors['access'] = [
    //         'class' => \yii\filters\AccessControl::class,
    //         'only' => ['load-chat-json'], // nama action
    //         'rules' => [
    //             [
    //                 'allow' => true,
    //                 'roles' => ['?', '@'], // ? = guest, @ = logged-in
    //             ],
    //         ],
    //     ];

    //     return $behaviors;
    // }


    /**
     * Lists all Roomchat models.
     *
     * @return string
     */
    // public function actionIndex()
    // {
    //   $searchModel = new RoomchatSearch();
    //   $dataProvider = $searchModel->search($this->request->queryParams);

    //   return $this->render('index', [
    //   'searchModel' => $searchModel,
    //   'dataProvider' => $dataProvider,
    //   ]);
    // }
    public function actionIndex()
    {
        $roomList = Roomchat::find()->orderBy(['send_at' => SORT_DESC])->all();

        return $this->render('index', [
            'roomList' => $roomList,
        ]);
    }

    /**
     * Displays a single Roomchat model.
     * @param int $id_chat Id Chat
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id_chat)
    {
        return $this->renderAjax('view', [
            'model' => $this->findModel($id_chat),
        ]);
    }

    /**
     * Creates a new Roomchat model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Roomchat();

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
     * Updates an existing Roomchat model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id_chat Id Chat
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id_chat)
    {
        $model = $this->findModel($id_chat);

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

    /**
     * Deletes an existing Roomchat model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id_chat Id Chat
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id_chat)
    {
        $model = $this->findModel($id_chat);
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
     * Finds the Roomchat model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id_chat Id Chat
     * @return Roomchat the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id_chat)
    {
        if (($model = Roomchat::findOne(['id_chat' => $id_chat])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionSendChat()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $model = new Roomchat();
        $model->id_customer = Yii::$app->request->post('id_customer');
        $model->id_staff = Yii::$app->user->id; // karena yang login adalah staff
        $model->chat = Yii::$app->request->post('chat');
        $model->send_at = date('Y-m-d H:i:s');
        $model->is_read = 0;

        if ($model->save()) {
            return [
                'success' => true,
                'id_customer' => $model->id_customer
            ];
        } else {
            return ['success' => false];
        }
    }

    // public function actionLoadChat($id_customer)
    // {
    //     $chatMessages = Roomchat::find()
    //         ->where(['id_customer' => $id_customer])
    //         ->orderBy(['send_at' => SORT_ASC])
    //         ->all();

    //     return $this->renderPartial('_chat_messages', [
    //         'chatMessages' => $chatMessages,
    //     ]);
    // }

    // public function actionLoadChatJson($id_customer)
    // {
    //     Yii::$app->response->format = Response::FORMAT_JSON;

    //     $chatMessages = \app\models\Roomchat::find()
    //         ->where(['id_customer' => $id_customer])
    //         ->orderBy(['send_at' => SORT_ASC])
    //         ->asArray() // Penting agar hasil bisa langsung dijadikan JSON
    //         ->all();

    //     return [
    //         'success' => true,
    //         'data' => $chatMessages,
    //     ];
    // }//(ini yg bener )

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

    return [
        'status' => 'success',
        'customer_name' => $chatMessages[0]->customer->customer_name ?? 'Chat',
        'messages' => $chatData,
    ];
}

}
