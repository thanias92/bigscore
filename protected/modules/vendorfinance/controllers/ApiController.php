<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\models\Deals;

class ApiController extends Controller
{
    public $enableCsrfValidation = false; // Matikan CSRF untuk endpoint API
    public function actionGetDealsData($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $deals = Deals::findOne($id);
        if ($deals) {
            return [
                'status' => 'success',
                'data' => [
                    'deals_id' => $deals->deals_id,
                    'price_product' => $deals->price_product,
                    'label_pemasukan' => $deals->label_deals,
                    'purchase_type' => $deals->purchase_type,
                    'purchase_date' => $deals->purchase_date,
                    'description' => $deals->description,
                    'total' => $deals->total,
                ],
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Deals tidak ditemukan',
            ];
        }
    }
}
