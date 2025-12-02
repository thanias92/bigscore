<?php

namespace app\modules\ticketing\controllers;

use Yii;
use yii\web\Controller;
use app\models\Ticket;
use yii\db\Expression;

class DashboardController extends Controller
{
    public function actionIndex()
    {
        $request = Yii::$app->request;
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Default: 7 hari terakhir jika tidak dipilih
        if (!$startDate || !$endDate) {
            $startDate = date('Y-m-d', strtotime('-7 days'));
            $endDate = date('Y-m-d');
        }

        // Kondisi waktu filter
        $dateCondition = ['between', 'DATE(date_ticket)', $startDate, $endDate];

        $totalWaiting = Ticket::find()->where(['LOWER(status_ticket)' => 'waiting'])->andWhere($dateCondition)->count();
        $totalOpen = Ticket::find()->where(['LOWER(status_ticket)' => 'open'])->andWhere($dateCondition)->count();
        $totalInProgress = Ticket::find()->where(['LOWER(status_ticket)' => 'in progress'])->andWhere($dateCondition)->count();
        $totalDone = Ticket::find()->where(['LOWER(status_ticket)' => 'done'])->andWhere($dateCondition)->count();
        $totalTicket = Ticket::find()->where($dateCondition)->count();

        $viaWA = Ticket::find()->where(['LOWER(via)' => 'whatsapp'])->andWhere($dateCondition)->count();
        $viaRoomchat = Ticket::find()->where(['LOWER(via)' => 'roomchat'])->andWhere($dateCondition)->count();
        $viaMandiri = Ticket::find()->where(['LOWER(via)' => 'ticket mandiri'])->andWhere($dateCondition)->count();

        return $this->render('index', [
            'totalWaiting' => $totalWaiting,
            'totalOpen' => $totalOpen,
            'totalInProgress' => $totalInProgress,
            'totalDone' => $totalDone,
            'totalTicket' => $totalTicket,
            'viaWA' => $viaWA,
            'viaRoomchat' => $viaRoomchat,
            'viaMandiri' => $viaMandiri,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }


    // (Opsional) Kalau kamu butuh JSON untuk AJAX frontend (Chart.js dinamis)
    public function actionSummary()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return [
            'status' => [
                'waiting' => Ticket::find()->where(['status_ticket' => 'waiting'])->count(),
                'open' => Ticket::find()->where(['status_ticket' => 'open'])->count(),
                'in_progress' => Ticket::find()->where(['status_ticket' => 'in_progress'])->count(),
                'done' => Ticket::find()->where(['status_ticket' => 'done'])->count(),
            ],
            'via' => [
                'wa' => Ticket::find()->where(['via' => 'wa'])->count(),
                'roomchat' => Ticket::find()->where(['via' => 'roomchat'])->count(),
                'mandiri' => Ticket::find()->where(['via' => 'mandiri'])->count(),
            ],
            'total' => Ticket::find()->count(),
        ];
    }
}
