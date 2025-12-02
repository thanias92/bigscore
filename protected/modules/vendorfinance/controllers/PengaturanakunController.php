<?php

namespace app\modules\vendorfinance\controllers;

use Yii;
use app\models\Pengaturanakun;
use app\modules\vendorfinance\PengaturanakunSearch;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

class PengaturanakunController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => ['delete' => ['POST']],
            ],
        ]);
    }

    private function ensureUploadFolders()
    {
        $logoDir = Yii::getAlias('@webroot/uploads/logo/');
        $ttdDir  = Yii::getAlias('@webroot/uploads/ttd/');

        if (!is_dir($logoDir)) mkdir($logoDir, 0777, true);
        if (!is_dir($ttdDir))  mkdir($ttdDir,  0777, true);

        return [$logoDir, $ttdDir];
    }

    public function actionIndex()
    {
        $searchModel  = new PengaturanakunSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $model        = Pengaturanakun::findOne(1);

        if (!$model) throw new NotFoundHttpException('Data tidak ditemukan.');

        /* === simpan via AJAX === */
        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {

            /** pastikan folder upload ada */
            [$logoPath, $ttdPath] = $this->ensureUploadFolders();

            /* ----- logo ----- */
            $model->logo = UploadedFile::getInstance($model, 'logo');
            if ($model->logo) {
                $logoName = 'logo_' . time() . '.' . $model->logo->extension;
                $model->logo->saveAs($logoPath . $logoName);
                $model->logo = $logoName;
            } else {
                $model->logo = $model->oldAttributes['logo'];
            }

            /* ----- ttd canvas base64 ----- */
            $ttdData = Yii::$app->request->post('ttd_data');
            if (!empty($ttdData)) {
                $bytes   = base64_decode(preg_replace('#^data:image/\\w+;base64,#i', '', $ttdData));
                $ttdName = 'ttd_' . time() . '.png';
                file_put_contents($ttdPath . $ttdName, $bytes);
                $model->ttd = $ttdName;
            } else {
                $model->ttd = $model->oldAttributes['ttd'];
            }

            /* save */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->save(false)) {
                return ['status' => 'success', 'message' => 'Pengaturan akun berhasil disimpan.'];
            }
            return ['status' => 'failed', 'message' => 'Gagal menyimpan data.', 'errors' => $model->getErrors()];
        }

        /* render */
        return $this->render('index', [
            'model'        => $model,
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionUpdate($pengaturanakun_id)
    {
        $model = $this->findModel($pengaturanakun_id);

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {

            [$logoPath, $ttdPath] = $this->ensureUploadFolders();

            // Handle file upload logo
            $uploadedLogo = UploadedFile::getInstance($model, 'logo');
            if ($uploadedLogo) {
                $logoName = 'logo_' . time() . '.' . $uploadedLogo->extension;
                $uploadedLogo->saveAs($logoPath . $logoName);
                $model->logo = $logoName;
            } else {
                $model->logo = $model->oldAttributes['logo'];
            }

            // Handle TTD dari canvas base64
            $ttdData = Yii::$app->request->post('ttd_data');
            if (!empty($ttdData)) {
                $bytes   = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $ttdData));
                $ttdName = 'ttd_' . time() . '.png';
                file_put_contents($ttdPath . $ttdName, $bytes);
                $model->ttd = $ttdName;
            } else {
                $model->ttd = $model->oldAttributes['ttd'];
            }

            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->save(false)) {
                return [
                    'status' => 'success',
                    'message' => 'Berhasil Mengubah Data',
                    'redirect' => \yii\helpers\Url::to(['index'])
                ];
            }

            return [
                'status' => 'failed',
                'message' => 'Gagal Mengubah Data',
                'errors' => $model->getErrors(),
            ];
        }

        return $this->renderAjax('update', ['model' => $model]);
    }

    public function actionDelete($pengaturanakun_id)
    {
        $model = $this->findModel($pengaturanakun_id);
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $model->delete()
            ? ['status' => 'success', 'message' => 'Berhasil Menghapus Data']
            : ['status' => 'failed', 'message' => 'Gagal Menghapus Data'];
    }

    public function actionView($pengaturanakun_id)
    {
        return $this->renderAjax('view', ['model' => $this->findModel($pengaturanakun_id)]);
    }

    protected function findModel($id)
    {
        if (($m = Pengaturanakun::findOne($id)) !== null) return $m;
        throw new NotFoundHttpException('Halaman tidak ditemukan.');
    }
}
