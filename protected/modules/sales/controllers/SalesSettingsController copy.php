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
                        'roles' => ['Sales Manager'], // Hanya Sales Manager yang bisa akses
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        // Ambil semua user yang merupakan salesman
        $auth = Yii::$app->authManager;
        $salesManagerIDs = $auth->getUserIdsByRole('Sales Manager');
        $salesRepIDs = $auth->getUserIdsByRole('Sales Representative');
        $salesmenIDs = array_merge($salesManagerIDs, $salesRepIDs);

        // Ambil atau buat profil untuk setiap salesman
        $profiles = [];
        foreach ($salesmenIDs as $id) {
            $profile = SalesmanProfile::findOne(['user_id' => $id]);
            if (!$profile) {
                $profile = new SalesmanProfile(['user_id' => $id]);
            }
            $profiles[] = $profile;
        }

        // Jika form di-submit untuk disimpan
        if (Model::loadMultiple($profiles, Yii::$app->request->post()) && Model::validateMultiple($profiles)) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                foreach ($profiles as $profile) {
                    $profile->save(false); // Simpan tanpa validasi ulang
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
