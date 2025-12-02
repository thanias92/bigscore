<?php

namespace app\modules\task\controllers;

use Yii;
use app\models\Implementation;
use app\models\ImplementationDetail;
use app\models\Customer;
use app\modules\task\ImplementationSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ArrayDataProvider;
use yii\web\Response;

//kanza push ulang untuk hosting
class ImplementationController extends Controller
{
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


  public function actionIndex()
  {
    $isAjax = Yii::$app->request->isAjax;
    Yii::info('Is AJAX request: ' . ($isAjax ? 'YES' : 'NO'));

    $searchModel  = new ImplementationSearch();
    $params       = Yii::$app->request->queryParams;
    $dataProvider = $searchModel->search($params);

    if ($isAjax) {
      return $this->renderPartial('_gridview_partial', [
        'dataProvider' => $dataProvider,
        'searchModel' => $searchModel,
      ]);
    }

    $customers      = Customer::find()->orderBy('customer_name')->all();
    $jenisFilter    = array('customer_name' => 'Nama Pelanggan', 'email' => 'Email', 'no_telp' => 'No Telp', 'kontak_pribadi' => 'Kontak Pribadi', 'nama_produk' => 'Nama Produk', 'duration' => 'Duration', 'status' => 'Satus');
    return $this->render('index', [
      'dataProvider' => $dataProvider,
      'searchModel' => $searchModel,
      'customers' => $customers,
      'jenisFilter' => $jenisFilter
    ]);
  }


  public function actionAlert($deals_id)
  {
    return $this->renderAjax('alert', compact('deals_id'));
  }

  public function actionProses($deals_id)
  {

    $isAjax         = Yii::$app->request->isAjax;
    $searchModel    = new ImplementationSearch();
    $params         = Yii::$app->request->queryParams;
    $dataProvider   = $searchModel->search_implementasi($deals_id, $params);

    if (count($dataProvider) == 0) {
      $this->renderTemplate($deals_id);
      $dataProvider   = $searchModel->search_implementasi($deals_id, $params);
    }


    $customers = Customer::find()
    ->alias('c')
    ->innerJoin('deals d', 'd.customer_id = c.customer_id')
    ->innerJoin('pemasukan p', 'p.deals_id = d.deals_id')
    ->innerJoin('contract', 'contract.invoice_id = p.pemasukan_id')
    ->where(['c.deleted_at' => null])
    ->select([
        'c.*',
        'd.deals_id',
        'p.pemasukan_id',
        'contract.contract_id'
    ])
    ->orderBy('c.customer_name')
    ->asArray()
    ->all();


    $jenisFilter    = array('customer_name' => 'Nama Pelanggan', 'email' => 'Email', 'no_telp' => 'No Telp', 'kontak_pribadi' => 'Kontak Pribadi', 'nama_produk' => 'Nama Produk', 'duration' => 'Duration', 'status' => 'Satus');
    $customersDeal  = Customer::find()
      ->join('INNER JOIN', 'deals', 'deals.customer_id = customer.customer_id')
      ->where('deals.deals_id = :id', [':id' => $deals_id])
      ->orderBy('customer_name')
      ->one();

    return $this->render('proses', [
      'dataProvider' => $dataProvider,
      'customers' => $customers,
      'jenisFilter' => $jenisFilter,
      'customersDeal' => $customersDeal,
      'deal' => $deals_id,
    ]);
  }

  public function renderTemplate($deals_id){
      $template                             = array();

      $template['Persiapan']                = ["Persiapan pekerjaan", "User requirements", "Penyiapan data master", "Pengadaan server (on premise)", "Pengajuan bridging BPJS"];
      $template['Development']              = ["Install Operating System Server", "Install dan konfigurasi Emesys", "Import data master", "Konfigurasi dan testing"];
      $template['Training']                 = ["Pelatihan pemakaian Emesys", "Simulasi"];
      $template['Go_Live']                  = ["Migrasi data master stok", "Migrasi data master pasien"];
      $template['Support_dan_Maintentance'] = ["Monitoring penggunaan Emesys"];

      foreach ($template as $key => $value) {
        $modelImplementasi                  = new Implementation();
        $modelImplementasi->activity_title  = str_replace("_", " ", $key);
        $modelImplementasi->status          = "Open";
        $modelImplementasi->deals_id        = $deals_id;
        $modelImplementasi->created_at      = date('Y-m-d H:i:s');
        $modelImplementasi->created_by      = Yii::$app->user->id;

        foreach ($value as $item) {
          if ($modelImplementasi->save()) {
            $modelImplementasiDetail                  = new ImplementationDetail();
            $modelImplementasiDetail->id_implementasi = $modelImplementasi->id_implementasi;
            $modelImplementasiDetail->activity        = $item;
            $modelImplementasiDetail->detail          = "";
            $modelImplementasiDetail->start_date      = "";
            $modelImplementasiDetail->completion_date = "";
            $modelImplementasiDetail->pic_aktivitas   = "";
            $modelImplementasiDetail->notes           = "";
            $modelImplementasiDetail->created_by      = Yii::$app->user->id;
            $modelImplementasiDetail->created_at      = date('Y-m-d H:i:s');
            $modelImplementasiDetail->status          = "Open";

            if (!$modelImplementasiDetail->save()) {
              $allDetailSaved = false;
              break;
            }
          }
        }

      }
      

  }


  public function actionView($id_implementasi)
  {
    $status = array("Open", "In Progress", "Done");
    return $this->renderAjax('view', [
      'status' => $this->findModel($id_implementasi),
    ]);
  }

  public function actionCreate($deals_id)
  {
    // $model = $this->findModel($deals_id);
    if (Yii::$app->request->isPost) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      $post = Yii::$app->request->post();

      $modelImplementasi = new Implementation();
      $modelImplementasi->activity_title = $post['judul'] ?? null;
      $modelImplementasi->status = $post['status'] ?? null;
      $modelImplementasi->deals_id = $deals_id;
      $modelImplementasi->created_at = date('Y-m-d H:i:s');
      $modelImplementasi->created_by = Yii::$app->user->id;

      if ($modelImplementasi->save()) {
        $allDetailSaved = true;

        foreach ($post['detail'] as $value) {
          $modelImplementasiDetail = new ImplementationDetail();
          $modelImplementasiDetail->id_implementasi = $modelImplementasi->id_implementasi;
          $modelImplementasiDetail->activity = $value['aktivitas'];
          $modelImplementasiDetail->detail = $value['detail'];
          $modelImplementasiDetail->start_date = $value['start_date'];
          $modelImplementasiDetail->completion_date = $value['end_date'];
          $modelImplementasiDetail->pic_aktivitas = $value['pic'];
          $modelImplementasiDetail->notes = $value['catatan'];
          $modelImplementasiDetail->created_by = Yii::$app->user->id;
          $modelImplementasiDetail->created_at = date('Y-m-d H:i:s');
          $modelImplementasiDetail->status             = $post['status'];

          if (!$modelImplementasiDetail->save()) {
            $allDetailSaved = false;
            break;
          }
        }

        if ($allDetailSaved) {
          return [
            'status' => 'success',
            'message' => 'Data berhasil disimpan.'
          ];
        } else {
          return [
            'status' => 'failed',
            'message' => 'Detail implementasi gagal disimpan.',
            'errors' => $modelImplementasiDetail->errors
          ];
        }
      } else {
        return [
          'status' => 'failed',
          'message' => 'Data implementasi gagal disimpan.',
          'errors' => $modelImplementasi->errors
        ];
      }
    }

    $statusProgress = ["Open", "In Progress", "Done"];
    return $this->renderAjax('create', [
      'model' => [],
      'statusProgress' => $statusProgress,
      'detailList' => [],
    ]);
  }


  public function actionUpdate($deals_id, $id_implementasi)
  {
    $modelImplementasi = Implementation::findOne($id_implementasi);

    if (!$modelImplementasi) {
      throw new NotFoundHttpException("Data tidak ditemukan");
    }

    if (Yii::$app->request->isPost) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      $post = Yii::$app->request->post();

      $modelImplementasi->activity_title = $post['judul'] ?? null;
      $modelImplementasi->status         = $post['status'] ?? null;
      $modelImplementasi->updated_at     = date('Y-m-d H:i:s');
      $modelImplementasi->updated_by     = Yii::$app->user->id;

      if ($modelImplementasi->save()) {

        ImplementationDetail::deleteAll(['id_implementasi' => $id_implementasi]);

        foreach ($post['detail'] as $value) {
          $detail = new ImplementationDetail();
          $detail->id_implementasi   = $modelImplementasi->id_implementasi;
          $detail->activity          = $value['aktivitas'];
          $detail->detail            = $value['detail'];
          $detail->start_date        = $value['start_date'];
          $detail->completion_date   = $value['end_date'];
          $detail->pic_aktivitas     = $value['pic'];
          $detail->notes             = $value['catatan'];
          $detail->created_by        = Yii::$app->user->id;
          $detail->created_at        = date('Y-m-d H:i:s');
          //   if ($post['status'] == "Done") {
          $detail->status             = $post['status'];
          //   }
          $detail->save();
        }

        return [
          'status' => 'success',
          'message' => 'Data berhasil diupdate'
        ];
      } else {
        return [
          'status' => 'failed',
          'message' => 'Gagal menyimpan data implementasi',
          'errors' => $modelImplementasi->getErrors()
        ];
      }
    }

    $statusProgress = ["Open", "In Progress", "Done"];
    $detailList = ImplementationDetail::find()
      ->where(['id_implementasi' => $modelImplementasi->id_implementasi])
      ->asArray()
      ->all();

    return $this->renderAjax('update', [
      'model' => $modelImplementasi,
      'statusProgress' => $statusProgress,
      'detailList' => $detailList,
    ]);
  }

  public function actionUpdateStatus()
  {
    Yii::$app->response->format = Response::FORMAT_JSON;
    $id                         = Yii::$app->request->post('id_implementasi');
    $status                     = Yii::$app->request->post('status');

    $modelImplementasi          = Implementation::findOne($id);
    if ($modelImplementasi) {
      $modelImplementasi->status = $status;
      if ($modelImplementasi->save(false)) {

        $detailList = ImplementationDetail::find()
          ->where(['id_implementasi' => $modelImplementasi->id_implementasi])
          ->all();

        foreach ($detailList as $detail) {
          $detail->status = $status;
          $detail->save(false);
        }

        return ['success' => true];
      }
    }

    return ['success' => false];
  }

  public function actionUpdateStatusDetail()
  {
    Yii::$app->response->format = Response::FORMAT_JSON;
    $id                         = Yii::$app->request->post('id_implementasi');
    $status                     = Yii::$app->request->post('status');

    $modelImplementasi          = ImplementationDetail::findOne($id);
    if ($modelImplementasi) {
      $modelImplementasi->status = $status;
      if ($modelImplementasi->save(false)) {
        return ['success' => true];
      }
    }

    return ['success' => false];
  }


  public function actionUpdatedetail($id_implementasi_detail)
  {
    $modelImplementasi = ImplementationDetail::findOne($id_implementasi_detail);

    if (!$modelImplementasi) {
      throw new NotFoundHttpException("Data tidak ditemukan");
    }

    if (Yii::$app->request->isPost) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      $post = Yii::$app->request->post();

      $modelImplementasi->activity            = $post['aktivitas'];
      $modelImplementasi->detail              = $post['detail'];
      $modelImplementasi->start_date          = $post['start_date'];
      $modelImplementasi->completion_date     = $post['end_date'];
      $modelImplementasi->pic_aktivitas       = $post['pic'];
      $modelImplementasi->notes               = $post['catatan'];
      $modelImplementasi->status              = $post['status'];
      $modelImplementasi->updated_at          = date('Y-m-d H:i:s');
      $modelImplementasi->updated_by          = Yii::$app->user->id;

      if ($modelImplementasi->save()) {
        return [
          'status' => 'success',
          'message' => 'Data berhasil diupdate'
        ];
      } else {
        return [
          'status' => 'failed',
          'message' => 'Gagal menyimpan data implementasi',
          'errors' => $modelImplementasi->getErrors()
        ];
      }
    }

    $statusProgress = ["Open", "In Progress", "Done"];

    return $this->renderAjax('_formdetail', [
      'model' => $modelImplementasi,
      'statusProgress' => $statusProgress
    ]);
  }

  public function actionDelete($id)
  {
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

    $model = Implementation::findOne($id);

    if (!$model) {
      return ['status' => 'error', 'message' => 'Data tidak ditemukan.'];
    }

    if ($model->delete()) {
      ImplementationDetail::deleteAll(['id_implementasi' => $id]);
      return ['status' => 'success', 'message' => 'Data berhasil dihapus.'];
    }

    return ['status' => 'error', 'message' => 'Gagal menghapus data.'];
  }

  public function actionDeletedetail($id)
  {
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

    $model = ImplementationDetail::findOne($id);

    if (!$model) {
      return ['status' => 'error', 'message' => 'Data tidak ditemukan.'];
    }

    ImplementationDetail::deleteAll(['id_implementasi_detail' => $id]);
    return ['status' => 'success', 'message' => 'Data berhasil dihapus.'];

    return ['status' => 'error', 'message' => 'Gagal menghapus data.'];
  }


  protected function findModel($id_implementasi)
  {
    if (($model = Implementation::findOne(['id_implementasi' => $id_implementasi])) !== null) {
      return $model;
    }

    throw new NotFoundHttpException('The requested page does not exist.');
  }
}
