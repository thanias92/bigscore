<?php

namespace app\modules\v1\controllers;

use app\models\Pemasukan;
use Yii;
use yii\rest\Controller;
use yii\web\Response;
use sizeg\jwt\JwtHttpBearerAuth;
use app\models\Roomchat;
use app\models\Ticket;
use app\modules\ticketing\controllers\TicketingController;
use yii\filters\ContentNegotiator;
use app\models\NotificationContract; // Atau sesuaikan lokasi model


class ApiemesysController extends Controller
{
    public $modelClass = "app\models\Tenant";

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;
        $behaviors['authenticator'] = [
            'class' => JwtHttpBearerAuth::class,
        ];

        return $behaviors;
    }

    public function actionAdd()
    {
        $jsonPayload = Yii::$app->request->getRawBody();
        $data = json_decode($jsonPayload, true);
        $id_customer = $data['id_customer'];
        $via = "Ticket Mandiri";
        $role = "customer";
        $modul = $data['modul'];
        $title = $data['title'];
        $description = $data['description'];
        $status_ticket = "Waitting";
        $code_ticket = TicketingController::code_ticket();

        $ticket = new Ticket();
        $ticket->id_deals = $id_customer;
        $ticket->via = $via;
        $ticket->code_ticket = $code_ticket;
        $ticket->modul = $modul;
        $ticket->role = $role;
        $ticket->title = $title;
        $ticket->description = $description;
        $ticket->status_ticket = $status_ticket;
        $ticket->date_ticket = date('Y-m-d H:i:s');
        // }
        if ($ticket->save()) {
            $response =
                [
                    'response' => $title,
                    'metadata' => [
                        'code' => 200,
                        'message' => "Pesan Berhasil Di Kirim"
                    ]
                ];
        } else {
            $response =
                [
                    'response' => $title,
                    'metadata' => [
                        'code' => 500,
                        'message' => "Terjadi kesalahan dalam memproses permintaan."
                    ]
                ];
        }
        return $response;
    }
    public function actionTicketMandiri()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        try {
            $jsonPayload = Yii::$app->request->getRawBody();
            $data = json_decode($jsonPayload, true);

            // Validasi input wajib
            if (!isset($data['title'], $data['modul'], $data['user'], $data['description'])) {
                return [
                    'response' => null,
                    'metadata' => [
                        'code' => 400,
                        'message' => "Data tidak lengkap. Pastikan title, modul, user, dan description diisi."
                    ]
                ];
            }

            $ticket = new Ticket();
            $ticket->title = $data['title'];
            $ticket->modul = $data['modul'];
            $ticket->description = $data['description'];
            $ticket->via = 'Ticket Mandiri';
            $ticket->role = 'customer';
            $ticket->status_ticket = 'Waiting';
            $ticket->code_ticket = TicketingController::code_ticket();
            $ticket->date_ticket = date('Y-m-d H:i:s');
            $ticket->created_at = date('Y-m-d H:i:s');
            $ticket->user = $data['user']; // varchar, misal "didi"

            // Opsional: kalau kamu masih ingin isi id_deals
            if (isset($data['id_customer'])) {
                $deals = \app\models\Deals::find()
                    ->where(['customer_id' => $data['id_customer']])
                    ->one();

                if ($deals) {
                    $ticket->id_deals = $deals->deals_id;
                }
            }

            if ($ticket->save()) {
                return [
                    'response' => [
                        'id_ticket' => $ticket->id_ticket,
                        'code_ticket' => $ticket->code_ticket,
                        'title' => $ticket->title,
                        'modul' => $ticket->modul,
                        'status_ticket' => $ticket->status_ticket,
                        'date_ticket' => $ticket->date_ticket,
                    ],
                    'metadata' => [
                        'code' => 200,
                        'message' => "Ticket berhasil dikirim."
                    ]
                ];
            } else {
                return [
                    'response' => $ticket->getErrors(),
                    'metadata' => [
                        'code' => 500,
                        'message' => "Gagal menyimpan ticket. Silakan periksa data yang dikirim."
                    ]
                ];
            }
        } catch (\Throwable $e) {
            return [
                'response' => null,
                'metadata' => [
                    'code' => 500,
                    'message' => "Terjadi kesalahan pada server: " . $e->getMessage()
                ]
            ];
        }
    }





    public function actionHistoryComplaint($id_customer)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $deals = \app\models\Deals::find()
            ->where(['customer_id' => $id_customer])
            ->one();
        $ticketall = Ticket::find()
            ->where(['id_deals' => $deals->deals_id])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        $data_ticket = [];

        foreach ($ticketall as $i_ticket) {

            $data_ticket[] = [
                'id_complaint' => $i_ticket->code_ticket,
                'date' => $i_ticket->date_ticket,
                'via' => $i_ticket->via,
                'user' => $i_ticket->user,
                'modul' => $i_ticket->modul,
                'title' => $i_ticket->title,
                'status' => $i_ticket->status_ticket,
            ];
        }
        $response =
            [
                'response' => [
                    'data' => $data_ticket
                ],
                'metadata' => [
                    'code' => 200,
                    'message' => "success"
                ]
            ];
        return $response;
    }

    public function actionHistoryPayment($id_customer)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $dealsIds = \app\models\Deals::find()
                ->select('deals_id')
                ->where(['customer_id' => $id_customer])
                ->column();

            if (empty($dealsIds)) {
                return [
                    'response' => [
                        'data' => []
                    ],
                    'metadata' => [
                        'code' => 404,
                        'message' => 'No deals found for this customer.'
                    ]
                ];
            }

            $pemasukanList = \app\models\Pemasukan::find()
                ->where(['deals_id' => $dealsIds])
                ->andWhere(['status' => 'Lunas'])
                ->with('penerimaanPembayarans')
                // ->andWhere(['deleted_at' => null])

                ->orderBy(['tgl_jatuhtempo' => SORT_DESC])
                ->all();

            if (!$pemasukanList || count($pemasukanList) === 0) {
                return [
                    'response' => [
                        'data' => []
                    ],
                    'metadata' => [
                        'code' => 404,
                        'message' => 'No pemasukan found for this customer.'
                    ]
                ];
            }

            $data_pemasukan = [];
            foreach ($pemasukanList as $item) {
                $tanggalList = [];
                foreach ($item->penerimaanPembayarans as $p) {
                    $tanggalList[] = $p->tanggal_bukti_transfer;
                }
                $data_pemasukan[] = [
                    'tgl_jatuhtempo' => $item->tgl_jatuhtempo,
                    'tanggal_bukti_transfer' => $tanggalList,
                    'no_faktur' => $item->no_faktur,
                    'grand_total' => number_format($item->grand_total),
                    'status' => $item->status,
                ];
            }

            return [
                'response' => [
                    'data' => $data_pemasukan
                ],
                'metadata' => [
                    'code' => 200,
                    'message' => 'success'
                ]
            ];
        } catch (\Throwable $e) {
            Yii::error("HistoryPayment Error: " . $e->getMessage(), __METHOD__);

            return [
                'response' => [],
                'metadata' => [
                    'code' => 500,
                    'message' => 'Internal Server Error: ' . $e->getMessage()
                ]
            ];
        }
    }
    public function actionAddFeedback()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $jsonPayload = Yii::$app->request->getRawBody();
            $data = json_decode($jsonPayload, true);

            if (!isset($data['id_customer'])) {
                return [
                    'response' => [],
                    'metadata' => [
                        'code' => 400,
                        'message' => "id_customer tidak ditemukan dalam request"
                    ]
                ];
            }

            $id_customer = $data['id_customer'];

            // Ambil deals berdasarkan customer_id
            $deals = \app\models\Deals::find()
                ->where(['customer_id' => $id_customer])
                ->one();

            if (!$deals) {
                return [
                    'response' => [],
                    'metadata' => [
                        'code' => 404,
                        'message' => "Deals tidak ditemukan"
                    ]
                ];
            }

            // Ambil semua pemasukan berdasarkan deals_id
            $pemasukanList = \app\models\Pemasukan::find()
                ->where(['deals_id' => $deals->deals_id])
                ->all();

            if (empty($pemasukanList)) {
                return [
                    'response' => [],
                    'metadata' => [
                        'code' => 404,
                        'message' => "Data pemasukan tidak ditemukan"
                    ]
                ];
            }

            // Ambil kontrak berdasarkan invoice_id dari semua pemasukan
            $contracts = \app\models\Contract::find()
                ->where(['invoice_id' => array_column($pemasukanList, 'pemasukan_id')])
                ->orderBy(['start_date' => SORT_ASC])
                ->all();

            if (empty($contracts)) {
                return [
                    'response' => [],
                    'metadata' => [
                        'code' => 404,
                        'message' => "Kontrak tidak ditemukan"
                    ]
                ];
            }

            // Ambil start_date kontrak paling awal
            $earliestContract = $contracts[0];
            if (!$earliestContract->start_date) {
                return [
                    'response' => [],
                    'metadata' => [
                        'code' => 404,
                        'message' => "Start date kontrak tidak tersedia"
                    ]
                ];
            }

            // Hitung interval dari start_date ke hari ini
            $startDate = new \DateTime($earliestContract->start_date);
            $now = new \DateTime();
            $interval = $startDate->diff($now);
            $totalMonths = ($interval->y * 12) + $interval->m;
            $expectedFeedbackCount = floor($totalMonths / 6); // Setiap 6 bulan 1x feedback

            // Cek jumlah feedback yang sudah diberikan
            $existingFeedbackCount = \app\models\Feedback::find()
                ->where(['id_deals' => $deals->deals_id])
                ->count();

            if ($existingFeedbackCount >= $expectedFeedbackCount) {
                return [
                    'response' => [],
                    'metadata' => [
                        'code' => 403,
                        'message' => "Belum waktunya mengisi feedback berikutnya"
                    ]
                ];
            }

            // Validasi inputan feedback
            if (!isset($data['rate']) || !isset($data['feedback'])) {
                return [
                    'response' => [],
                    'metadata' => [
                        'code' => 400,
                        'message' => "Data rate atau feedback tidak lengkap"
                    ]
                ];
            }

            // Simpan feedback
            $feedback = new \app\models\Feedback();
            $feedback->id_deals = $deals->deals_id;
            $feedback->rate = (string)$data['rate'];
            $feedback->feedback = $data['feedback'];
            $feedback->date_feedback = date('Y-m-d');

            if ($feedback->save()) {
                return [
                    'response' => $feedback->attributes,
                    'metadata' => [
                        'code' => 200,
                        'message' => "Feedback berhasil disimpan"
                    ]
                ];
            } else {
                return [
                    'response' => $feedback->getErrors(),
                    'metadata' => [
                        'code' => 500,
                        'message' => "Gagal menyimpan feedback"
                    ]
                ];
            }
        } catch (\Throwable $e) {
            return [
                'response' => [],
                'metadata' => [
                    'code' => 500,
                    'message' => "Terjadi kesalahan: " . $e->getMessage()
                ]
            ];
        }
    }
}
