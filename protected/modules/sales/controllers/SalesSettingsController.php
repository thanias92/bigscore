<?php

namespace app\modules\sales\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\models\User;
use app\models\SalesmanProfile;
use yii\base\Model;

class SalesSettingsController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['Sales Manager'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        // 1. Dapatkan semua ID salesman
        $auth = Yii::$app->authManager;
        $salesManagerIDs = $auth->getUserIdsByRole('Sales Manager');
        $salesRepIDs = $auth->getUserIdsByRole('Sales Representative');
        $salesmenIDs = array_merge($salesManagerIDs, $salesRepIDs);

        if (empty($salesmenIDs)) {
            return $this->render('index', ['profiles' => []]);
        }

        // 2. Ambil semua objek User salesman DAN profil mereka dalam satu query (Eager Loading)
        $salesmen = User::find()
            ->with('salesmanProfile') // <-- Kunci perbaikan
            ->where(['id' => $salesmenIDs])
            ->all();

        // 3. Siapkan array profil, pastikan setiap profil memiliki data user
        $profiles = [];
        foreach ($salesmen as $salesman) {
            if ($salesman->salesmanProfile) {
                // Jika profil sudah ada, gunakan yang sudah di-load
                $profile = $salesman->salesmanProfile;
            } else {
                // Jika belum ada, buat objek baru
                $profile = new SalesmanProfile(['user_id' => $salesman->id]);
                // "Suntikkan" objek user ke dalam relasi profil secara manual
                $profile->populateRelation('user', $salesman);
            }
            $profiles[] = $profile;
        }

        // Logika untuk menyimpan data (tidak berubah)
        if (Model::loadMultiple($profiles, Yii::$app->request->post()) && Model::validateMultiple($profiles)) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                foreach ($profiles as $profile) {
                    $profile->save(false);
                }
                $transaction->commit();
                Yii::$app->session->setFlash('success', 'Sales targets have been updated successfully.');
                return $this->refresh();
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Failed to update sales targets.');
            }
        }

        return $this->render('index', ['profiles' => $profiles]);
    }
}
