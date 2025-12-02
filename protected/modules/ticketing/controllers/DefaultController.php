<?php

namespace app\modules\ticketing\controllers;

use yii\web\Controller;
use yii\web\Response;
use app\models\Ticket;
use app\models\Customer; // Asumsi ada model Customer
use app\models\Staff;   // Asumsi ada model Staff

class DefaultController extends Controller
{
    public function actionIndex($source = 'staff')
    {
        $ticketData = [];
    
        if ($source === 'staff') {
            $ticketData = Ticket::find()
                ->with('customer') // Pastikan relasi 'customer' ada
                ->all();
        }
    
        return $this->render('index', [
            'ticketData' => $ticketData,
            'source' => $source,
        ]);
    }
}