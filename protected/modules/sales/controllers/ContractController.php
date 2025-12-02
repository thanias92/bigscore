<?php

namespace app\modules\sales\controllers;

use Yii;
use app\models\Contract;
use app\models\Pemasukan;
use app\modules\sales\ContractSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\UploadedFile;

class ContractController extends Controller
{
  public function behaviors()
  {
    return array_merge(parent::behaviors(), [
      'verbs' => [
        'class' => VerbFilter::className(),
        'actions' => [
          'delete' => ['POST'],
        ],
      ],
    ]);
  }

  public function actionIndex()
  {
    $searchModel = new ContractSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    // Kirim HANYA searchModel dan dataProvider
    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ]);
  }

  public function actionCreate()
  {
    $model = new Contract();

    // SET DEFAULT DATE HARI INI SAAT FORM PERTAMA KALI DIBUKA
    $model->start_date = date('Y-m-d');
    // SET DEFAULT DATE END DATE SAAT FORM PERTAMA KALI DIBUKA
    $model->end_date = date('Y-m-d', strtotime('+1 year'));

    // Generate contract code
    $lastContract = Contract::find()
      ->where(['like', 'contract_code', 'CON-%', false])
      ->andWhere(['deleted_at' => null])
      ->orderBy(['contract_id' => SORT_DESC])
      ->one();

    if ($lastContract && preg_match('/CON-(\d+)/', $lastContract->contract_code, $matches)) {
      $lastNumber = (int)$matches[1];
      $model->contract_code = 'CON-' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
    } else {
      $model->contract_code = 'CON-001';
    }

    $pemasukans = Pemasukan::find()
      ->alias('p')
      // Subquery untuk mencari invoice_id yang sudah ada di tabel contract
      ->where([
        'NOT IN',
        'p.pemasukan_id',
        Contract::find()->select('invoice_id')
      ])
      // Tetap pertahankan filter parent_id untuk kasus cicilan
      ->andWhere(['p.parent_id' => null, 'p.deleted_at' => null])
      ->with(['cicilanAnak.cicilanPertama', 'deals.customer'])
      ->all();

    if (Yii::$app->request->isPost) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      $model->load(Yii::$app->request->post());

      // GUNAKAN 'uploadFile' UNTUK MENGAMBIL INSTANCE
      $uploadedFile = UploadedFile::getInstance($model, 'uploadFile');
      if ($uploadedFile) {
        $fileName = 'evidence_' . time() . '.' . $uploadedFile->extension;
        $uploadPath = Yii::getAlias('@webroot/uploads/contracts/');
        if (!is_dir($uploadPath)) {
          mkdir($uploadPath, 0777, true);
        }
        if ($uploadedFile->saveAs($uploadPath . $fileName)) {
          // TETAP SIMPAN PATH KE 'evidence_contract' (KOLOM DB)
          $model->evidence_contract = 'uploads/contracts/' . $fileName;
        }
      }

      if ($model->save()) {
        return ['success' => true, 'message' => 'Data berhasil ditambahkan'];
      } else {
        return ['status' => 'failed', 'message' => 'Gagal menyimpan data', 'errors' => $model->getErrors()];
      }
    }

    if (Yii::$app->request->isAjax) {
      return $this->renderAjax('_form', [
        'model' => $model,
        'pemasukans' => $pemasukans,
        'mode' => 'create',
      ]);
    }

    return $this->render('create', [
      'model' => $model,
      'pemasukans' => $pemasukans,
    ]);
  }

  public function actionGetInfo($id)
  {
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    $invoice = Pemasukan::findOne($id);

    if (!$invoice || !$invoice->deals || !$invoice->deals->customer || !$invoice->deals->product) {
      return ['error' => 'Data tidak lengkap'];
    }

    return [
      'customer_name'    => $invoice->deals->customer->customer_name,
      'email'            => $invoice->deals->customer->customer_email,
      'product'          => $invoice->deals->product->product_name,
      'unit'             => $invoice->deals->unit_product,
      'price'            => $invoice->deals->price_product,
      'total'            => $invoice->deals->total,
      'sub_total'        => $invoice->sub_total,
      'diskon'           => $invoice->diskon,
      'grand_total'      => $invoice->grand_total,
    ];
  }

  public function actionView($contract_id)
  {
    $model = $this->findModel($contract_id);
    $pemasukans = \app\models\Pemasukan::find()->all(); // Data yang dibutuhkan form

    // Periksa apakah ini permintaan AJAX
    if (Yii::$app->request->isAjax) {
      // Jika YA, render _form sebagai konten modal
      return $this->renderAjax('_form', [
        'model' => $model,
        'pemasukans' => $pemasukans, // Kirim juga data ini ke form
        'mode' => 'view',
      ]);
    }

    // Jika BUKAN AJAX, render halaman view lengkap
    // Pastikan view.php juga menerima variabel yang dibutuhkan
    return $this->render('view', [
      'model' => $model,
      'pemasukans' => $pemasukans,
    ]);
  }

  public function actionUpdate($contract_id)
  {
    $model = $this->findModel($contract_id);
    $pemasukans = Pemasukan::find()->all();

    // Simpan path file lama DARI ATRIBUT DATABASE SEBELUM di-load
    $oldFile = $model->evidence_contract;

    if (Yii::$app->request->isPost) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      $model->load(Yii::$app->request->post());

      // GUNAKAN 'uploadFile' UNTUK MENGAMBIL INSTANCE
      $uploadedFile = UploadedFile::getInstance($model, 'uploadFile');
      if ($uploadedFile) {
        $fileName = 'evidence_' . time() . '.' . $uploadedFile->extension;
        $uploadPath = Yii::getAlias('@webroot/uploads/contracts/');
        if (!is_dir($uploadPath)) {
          mkdir($uploadPath, 0777, true);
        }
        if ($uploadedFile->saveAs($uploadPath . $fileName)) {
          // Hapus file lama jika ada dan berhasil upload baru
          if ($oldFile && file_exists(Yii::getAlias('@webroot/') . $oldFile)) {
            unlink(Yii::getAlias('@webroot/') . $oldFile);
          }
          // SIMPAN PATH BARU KE 'evidence_contract' (KOLOM DB)
          $model->evidence_contract = 'uploads/contracts/' . $fileName;
        }
      } else {
        // Jika tidak ada file baru di-upload, pertahankan file lama
        $model->evidence_contract = $oldFile;
      }

      if ($model->save()) {
        return ['success' => true, 'message' => 'Berhasil mengubah data'];
      } else {
        return ['status' => 'failed', 'message' => 'Gagal mengubah data', 'errors' => $model->getErrors()];
      }
    }

    return $this->renderAjax('_form', [
      'model' => $model,
      'pemasukans' => $pemasukans,
      'mode' => 'edit',
    ]);
  }

  public function actionDelete($contract_id)
  {
    Yii::$app->response->format = Response::FORMAT_JSON;
    if ($this->findModel($contract_id)->delete()) {
      return ['success' => true, 'message' => 'Berhasil Menghapus Data'];
    }
    return ['success' => false, 'message' => 'Gagal Menghapus Data'];
  }

  protected function findModel($contract_id)
  {
    if (($model = Contract::findOne(['contract_id' => $contract_id])) !== null) {
      return $model;
    }

    throw new NotFoundHttpException('Halaman yang diminta tidak ditemukan.');
  }
}
