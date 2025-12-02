<?php

namespace app\modules\ticketing\controllers;

use app\models\Deals;
use Yii;
use app\models\Ticket;
use app\modules\ticketing\TicketingSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\data\ActiveDataProvider;

/**
 * TicketingController implements the CRUD actions for Ticket model.
 */
class TicketingController extends Controller
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

  public function actionLoadByRole($role)
  {
    $searchModel = new TicketingSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    $dataProvider->query->andWhere(['role' => $role]);

    return $this->renderAjax('_ticket_grid', [
      'dataProvider' => $dataProvider,
      'searchModel' => $searchModel,
      'role' => $role,
    ]);
  }


  public function actionIndex($category = 'staff')
  {
    $searchModel = new TicketingSearch();
    $searchModel->role = $category; // inject ke model pencarian

    // Gunakan TicketingSearch agar pencarian aktif
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    // Tambahkan eager loading jika perlu
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    // Cek dulu apakah ada query-nya dan apakah query-nya instance ActiveQuery
    if ($dataProvider->query instanceof \yii\db\ActiveQuery) {
      $dataProvider->query->with(['deals.customer']);
    }

    // Tambahkan filter tambahan jika perlu
    if ($category === 'staff') {
      $dataProvider->query->andWhere(['ticket.role' => 'staff']);
    } elseif ($category === 'customer') {
      $dataProvider->query->andWhere(['ticket.role' => 'customer']);
    }

    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ]);
  }


  public function actionDashboard()
  {
    // Ambil semua data ticket
    $data = Ticket::find()
      ->select(['via', 'status_ticket'])
      ->asArray()
      ->all();

    // Hitung berdasarkan status (dibuat lowercase untuk konsistensi)
    $totalWaiting = Ticket::find()
      ->where(new \yii\db\Expression("LOWER(status_ticket) = 'waiting'"))
      ->count();

    $totalOpen = Ticket::find()
      ->where(new \yii\db\Expression("LOWER(status_ticket) = 'open'"))
      ->count();

    $totalInProgress = Ticket::find()
      ->where(new \yii\db\Expression("LOWER(status_ticket) = 'in_progress'"))
      ->count();

    $totalDone = Ticket::find()
      ->where(new \yii\db\Expression("LOWER(status_ticket) = 'done'"))
      ->count();

    $totalTicket = Ticket::find()->count();

    // Hitung berdasarkan media (via)
    $viaMandiri = Ticket::find()
      ->where(new \yii\db\Expression("LOWER(via) = 'mandiri'"))
      ->count();

    $viaRoomchat = Ticket::find()
      ->where(new \yii\db\Expression("LOWER(via) = 'roomchat'"))
      ->count();

    $viaWA = Ticket::find()
      ->where(new \yii\db\Expression("LOWER(via) = 'whatsapp'"))
      ->count();

    return $this->render('dashboard', [
      'totalWaiting' => $totalWaiting,
      'totalOpen' => $totalOpen,
      'totalInProgress' => $totalInProgress,
      'totalDone' => $totalDone,
      'totalTicket' => $totalTicket,
      'viaMandiri' => $viaMandiri,
      'viaRoomchat' => $viaRoomchat,
      'viaWA' => $viaWA,
    ]);
  }


  public function actionView($id_ticket)
  {
    $model = $this->findModel($id_ticket);

    $deals = \app\models\Deals::find()->with('customer')->all(); // ambil data deals + customer
    // Konversi format tanggal agar sesuai input[type=date]
    if (!empty($model->date_ticket)) {
      $model->date_ticket = date('Y-m-d', strtotime($model->date_ticket));
    }
    if (!empty($model->duedate)) {
      $model->duedate = date('Y-m-d', strtotime($model->duedate));
    }
    return $this->renderAjax('view', [
      'model' => $model,
      'deals' => $deals, // ← tambahkan ini
    ]);
  }


  public function actionCreate()
  {
    $model = new Ticket();
    $code_tiket = TicketingController::code_ticket();

    $model->code_ticket = $code_tiket;

    if ($this->request->isPost) {
      if ($model->load($this->request->post()) && $model->save()) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['status' => 'success', 'message' => 'Ticket created successfully.'];
      } else {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['status' => 'error', 'message' => 'Failed to create ticket.', 'errors' => $model->getErrors()];
      }
    } else {
      $model->loadDefaultValues();
    }

    $deals = Deals::find()->with('customer')->all();


    return $this->renderAjax('_form', [
      'model' => $model,
      'deals' => $deals,
    ]);
  }

  public function actionUpdate($id_ticket)
  {
    $model = $this->findModel($id_ticket);

    if ($model->load($this->request->post())) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      if ($model->save()) {
        return [
          'status' => 'success',
          'message' => 'Berhasil Mengubah Data'
        ];
      } else {
        return [
          'status' => 'failed',
          'message' => 'Gagal Mengubah Data ' . (!empty($model->errors) ? json_encode($model->errors) : '')
        ];
      }
    }
    // Konversi format tanggal agar sesuai input[type=date]
    if (!empty($model->date_ticket)) {
      $model->date_ticket = date('Y-m-d', strtotime($model->date_ticket));
    }

    // Ambil data Deals
    $deals = Deals::find()->with('customer')->all();
    return $this->renderAjax('update', [
      'model' => $model,
      'deals' => $deals, // kirim ke view
    ]);
  }

  public function actionDelete($id_ticket)
  {
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

    $model = Ticket::findOne($id_ticket);
    if ($model) {
      $model->delete();
      return ['status' => 'success', 'message' => 'Data berhasil dihapus.'];
    } else {
      return ['status' => 'error', 'message' => 'Data tidak ditemukan.'];
    }
  }

  public function actionGetDealsDetails($j)
  {
    Yii::$app->response->format = Response::FORMAT_JSON;

    $deals = Deals::find()->with('customer')->all(); // eager load relasi customer
    $result = [];

    foreach ($deals as $i) {
      if ($i->customer) {
        $result[] = [
          'id' => $i->deals_id, // typo sebelumnya: "delas_id"
          'text' => $i->customer_name,
          'email' => $i->customer_email,
        ];
      }
    }

    return $result;
  }


  public function actionGetInfo($id)
  {
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    $deals = Deals::find()->with('customer')->where(['deals_id' => $id])->one();

    if ($deals && $deals->customer) {
      return [
        'email' => $deals->customer->customer_email,
        'nama' => $deals->customer->customer_name,
      ];
    }

    return [
      'email' => null,
      'nama' => null,
    ];
  }


  protected function findModel($id_ticket)
  {
    if (($model = Ticket::findOne(['id_ticket' => $id_ticket])) !== null) {
      return $model;
    }

    throw new NotFoundHttpException('The requested page does not exist.');
  }

  public function getDeals()
  {
    return $this->hasOne(Deals::class, ['deals_id' => 'id_deals']);
  }

  public static function code_ticket()
  {
    $prefix = "TIC-";
    $lastTicket = Ticket::find()
      ->where(['like', 'code_ticket', $prefix . '%', false])
      ->andWhere(['deleted_at' => null])
      ->orderBy(['id_ticket' => SORT_DESC])
      ->one();

    if ($lastTicket && preg_match('/TIC-(\d{3})$/', $lastTicket->code_ticket, $matches)) {
      $newNumber = (int)$matches[1] + 1;
    } else {
      $newNumber = 1;
    }
    return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
  }
}
