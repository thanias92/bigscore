<?php

namespace app\modules\vendorfinance\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\helpers\Url;
use app\models\Pengeluaran;
use app\models\Vendor;
use app\models\Accountkeluar;
use app\modules\vendorfinance\PengeluaranSearch;

class PengeluaranController extends Controller
{
  public function behaviors()
  {
    return array_merge(parent::behaviors(), [
      'verbs' => [
        'class'   => VerbFilter::class,
        'actions' => ['delete' => ['POST']],
      ],
    ]);
  }

  public function actionIndex()
  {
    $searchModel  = new PengeluaranSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    return $this->render('index', compact('searchModel', 'dataProvider'));
  }

  public function actionView($id_pengeluaran)
  {
    return $this->renderAjax('view', [
      'model' => $this->findModel($id_pengeluaran),
    ]);
  }

  public function actionCreate()
  {
    $model = new Pengeluaran();

    // Generate no_pengeluaran
    $last = Pengeluaran::find()
      ->where(['like', 'no_pengeluaran', 'EXP-%', false])
      ->orderBy(['id_pengeluaran' => SORT_DESC])
      ->one();

    $model->no_pengeluaran = $last && preg_match('/EXP-(\d+)/', $last->no_pengeluaran, $m)
      ? 'EXP-' . str_pad(((int)$m[1]) + 1, 3, '0', STR_PAD_LEFT)
      : 'EXP-001';

    // 🔧 HANYA AKUN UNTUK PENGELUARAN
    $listAccount = \yii\helpers\ArrayHelper::map(
      Accountkeluar::find()
        ->where(['ilike', 'penggunaan', 'pengeluaran'])
        ->andWhere([
          'or',
          ['like', 'code', '5-%', false],
          ['like', 'code', '6-%', false],
          ['like', 'code', '8-%', false],
        ])
        ->orderBy('code')
        ->all(),
      'id',
      fn($m) => $m->code . ' ' . $m->akun
    );

    $listVendor = Vendor::find()
      ->where(['deleted_at' => null]) // tambahkan ini juga
      ->select(['nama_vendor', 'id_vendor'])
      ->orderBy('nama_vendor')
      ->indexBy('id_vendor')
      ->column();

    if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
      Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

      if (!$model->validate()) {
        return ['status' => 'failed', 'message' => $model->errors];
      }

      return $model->save()
        ? ['status' => 'success', 'message' => 'Berhasil menyimpan data.']
        : ['status' => 'failed', 'message' => $model->errors];
    }

    // ✅ Kirim ke view
    return $this->renderAjax('_form', [
      'model' => $model,
      'listAccount' => $listAccount,
      'listVendor' => $listVendor,
    ]);
  }

  public function actionUploadProof($id_pengeluaran)
  {
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    if (!Yii::$app->request->isPost) {
      return ['status' => 'failed', 'message' => 'Request bukan POST'];
    }

    $model = $this->findModel($id_pengeluaran);
    $file  = UploadedFile::getInstanceByName('bukti');
    if ($file === null) {
      return ['status' => 'failed', 'message' => 'File tidak ditemukan.'];
    }

    // Folder privat
    $privateDir = 'C:/MAGANG/Bigs/emesys_crm/protected/modules/vendorfinance/views/pengeluaran/bukti_pengeluaran';
    if (!is_dir($privateDir) && !mkdir($privateDir, 0775, true)) {
      return ['status' => 'failed', 'message' => 'Gagal membuat folder privat.'];
    }

    $fileName  = 'bukti_' . $id_pengeluaran . '_' . time() . '.' . $file->extension;
    $private   = $privateDir . DIRECTORY_SEPARATOR . $fileName;

    if (!$file->saveAs($private)) {
      return ['status' => 'failed', 'message' => 'Gagal menyimpan ke disk.'];
    }

    /* === SALIN ke folder publik ======================================= */
    $publicDir = Yii::getAlias('@webroot/uploads/pengeluaran');
    if (!is_dir($publicDir)) {
      mkdir($publicDir, 0775, true);
    }
    $public = $publicDir . DIRECTORY_SEPARATOR . $fileName;
    @copy($private, $public);                 // suppress warning kalau sudah ada
    /* ================================================================== */

    // update DB
    $model->bukti_pembayaran  = $fileName;
    $model->status_pembayaran = 'Sudah Dibayar';
    if (!$model->save(false, ['bukti_pembayaran', 'status_pembayaran'])) {
      return ['status' => 'failed', 'message' => 'Gagal menyimpan ke database.'];
    }

    return ['status' => 'success', 'message' => 'Bukti berhasil di‑upload.'];
  }

  public function actionGetProof($id_pengeluaran)
  {
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    $model = $this->findModel($id_pengeluaran);

    if (!$model->bukti_pembayaran) {
      return ['status' => 'failed', 'message' => 'Belum ada bukti pembayaran.'];
    }

    // path privat & publik
    $private = 'C:/MAGANG/Bigs/emesys_crm/protected/modules/vendorfinance/views/pengeluaran/bukti_pengeluaran/' . $model->bukti_pembayaran;
    $publicDir  = Yii::getAlias('@webroot/uploads/pengeluaran');
    if (!is_dir($publicDir)) {
      mkdir($publicDir, 0775, true);
    }
    $public = $publicDir . '/' . $model->bukti_pembayaran;

    // jika file publik belum ada → copy
    if (!file_exists($public)) {
      if (!file_exists($private)) {
        return ['status' => 'failed', 'message' => 'File tidak ditemukan di server.'];
      }
      @copy($private, $public);
    }

    $url = Url::to('@web/uploads/pengeluaran/' . $model->bukti_pembayaran, true);
    $ext = strtolower(pathinfo($model->bukti_pembayaran, PATHINFO_EXTENSION));

    return ['status' => 'success', 'url' => $url, 'ext' => $ext];
  }

  public function actionUpdate($id_pengeluaran)
  {
    $model = $this->findModel($id_pengeluaran);

    // ✅ Ambil list vendor
    $listVendor = Vendor::find()
      ->select(['nama_vendor', 'id_vendor'])
      ->orderBy('nama_vendor')
      ->indexBy('id_vendor')
      ->column();

    // ✅ Ambil hanya akun yang digunakan untuk 'pengeluaran'
    $listAccount = \yii\helpers\ArrayHelper::map(
      Accountkeluar::find()
        ->where(['ilike', 'penggunaan', 'pengeluaran'])
        ->andWhere([
          'or',
          ['like', 'code', '5-%', false],
          ['like', 'code', '6-%', false],
          ['like', 'code', '8-%', false],
        ])
        ->orderBy('code')
        ->all(),
      'id',
      fn($m) => $m->code . ' ' . $m->akun
    );

    if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
      Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

      if (!$model->validate()) {
        return ['status' => 'failed', 'message' => $model->errors];
      }

      return $model->save()
        ? ['status' => 'success', 'message' => 'Berhasil mengubah data.']
        : ['status' => 'failed', 'message' => $model->errors];
    }

    // ✅ Kembalikan tampilan _form yang sama untuk create dan update
    return $this->renderAjax('_form', [
      'model' => $model,
      'listVendor' => $listVendor,
      'listAccount' => $listAccount,
    ]);
  }

  public function actionAccountList($q = null)
  {
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

    $rows = Accountkeluar::find()
      ->where(['penggunaan' => 'pengeluaran'])
      ->andFilterWhere([
        'or',
        ['ilike', 'code', $q],
        ['ilike', 'akun', $q],
      ])
      ->select(['id AS id', "concat(code,' ',akun) AS text"])
      ->orderBy('code')
      ->limit(20)->asArray()->all();

    return ['results' => $rows];
  }

  public function actionVendorList($q = null)
  {
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    $query = \app\models\Vendor::find()
      ->select(['id as id', 'nama_vendor as text'])
      ->where(['like', 'nama_vendor', $q])
      ->limit(20)
      ->asArray()
      ->all();

    return ['results' => $query];
  }

  public function actionDelete()
  {
      $id = Yii::$app->request->post('id_pengeluaran');
      if (!$id) {
          return $this->asJson(['status' => 'error', 'message' => 'ID tidak ditemukan.']);
      }
  
      $model = $this->findModel($id);
      $model->deleted_at = date('Y-m-d H:i:s');
      $model->deleted_by = Yii::$app->user->id;
  
      if ($model->save(false)) {
          return $this->asJson(['status' => 'success', 'message' => 'Data berhasil dihapus.']);
      } else {
          return $this->asJson(['status' => 'error', 'message' => 'Gagal menghapus data.']);
      }
  }

  protected function findModel($id_pengeluaran)
  {
    if (($model = Pengeluaran::findOne($id_pengeluaran)) !== null) {
      return $model;
    }
    throw new NotFoundHttpException('Data tidak ditemukan.');
  }
}
