<?php

namespace app\modules\task\controllers;

use Yii;
use yii\web\Controller;
use app\models\Task;
use app\models\Ticket;
use app\models\Customer;
use app\models\Implementation as Implementasi;

use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\Url;

class DefaultController extends Controller
{
    public function actionIndex()
    {

        $task_customer_id             = Yii::$app->request->get('task_customer_id', '');
        $ticket_customer_id           = Yii::$app->request->get('ticket_customer_id', '');
        $implementasi_customer_id     = Yii::$app->request->get('implementasi_customer_id', '');

        if (empty($task_customer_id) && empty($ticket_customer_id) && empty($implementasi_customer_id)) {
            $taskHariini = $this->getTodayDeadlineTasks();
            if (!empty($taskHariini)) {
                $url = Url::to(['task/index', 'status' => 'Open']);
                $pesan = 'Hari ini ada deadline <strong>' . count($taskHariini) . '</strong> Task. ';
                $pesan .= '<a href="' . $url . '" class="btn btn-sm btn-danger ms-2">Lihat Task</a>';
                Yii::$app->session->setFlash('info', $pesan);
            }
        }

        $customers      = Customer::find()->orderBy('customer_name')->all();
        return $this->render('index', [
            'task'                      => $this->taskFunction($task_customer_id),
            'ticket'                    => $this->ticketFunction($ticket_customer_id),
            'implementasi'              => $this->implementasiFunction($implementasi_customer_id),
            'customers'                 => $customers,
            'task_customer_id'          => $task_customer_id,
            'ticket_customer_id'        => $ticket_customer_id,
            'implementasi_customer_id'  => $implementasi_customer_id
        ]);
    }
    private static function  getTodayDeadlineTasks($customerId = null)
    {
        $query = Task::find()
            ->where(['DATE(duedate_task)' => date('Y-m-d')])
            ->andWhere(['deleted_at' => null])
            ->andWhere(['finishdate_task' => null]);
        if (!empty($customerId)) {
            $query->andWhere(['customer_id' => $customerId]);
        }

        return $query->all();
    }

    public static function taskFunction($idCustomer = null)
    {
        // Filter hanya kalau idCustomer valid numeric
        if (!is_numeric($idCustomer) || empty($idCustomer)) {
            $idCustomer = null;
        }

        // Base query
        $tasksQuery = Task::find()->where(['deleted_at' => null]);

        if ($idCustomer !== null) {
            $tasksQuery->andWhere(['customer_id' => $idCustomer]);
        }

        // Data provider
        $dataProvider = new ActiveDataProvider([
            'query' => $tasksQuery,
            'pagination' => ['pageSize' => 5],
        ]);

        // Base condition
        $baseCondition = ['deleted_at' => null];
        if ($idCustomer !== null) {
            $baseCondition['customer_id'] = $idCustomer;
        }

        // Count
        $totalTask = Task::find()->where($baseCondition)->count();
        $taskOpen = Task::find()->where(array_merge($baseCondition, ['status' => 'Open']))->count();
        $taskInProgress = Task::find()->where(array_merge($baseCondition, ['status' => 'In Progress']))->count();
        $taskDone = Task::find()->where(array_merge($baseCondition, ['status' => 'Done']))->count();
        $taskWaiting = Task::find()->where(array_merge($baseCondition, ['status' => 'waiting']))->count();

        $taskLateQuery = Task::find()
            ->where(['<', 'duedate_task', date('Y-m-d')])
            ->andWhere(['!=', 'status', 'done'])
            ->andWhere(['deleted_at' => null]);

        if ($idCustomer !== null) {
            $taskLateQuery->andWhere(['customer_id' => $idCustomer]);
        }
        $taskLate = $taskLateQuery->count();

        // Return
        return [
            'totalTask'      => $totalTask,
            'taskOpen'       => $taskOpen,
            'taskInProgress' => $taskInProgress,
            'taskDone'       => $taskDone,
            'taskLate'       => $taskLate,
            'taskWaiting'    => $taskWaiting,
            'dataProvider'   => $dataProvider,
        ];
    }


    public static function ticketFunction($idCustomer = null)
    {
        $queryBase = Ticket::find()->where(['deleted_at' => null]);
        if ($idCustomer !== null && $idCustomer !== '') {
            $queryBase->andWhere(['customer_id' => $idCustomer]);
        }
        $totalTicket = $queryBase->count();

        $queryOpen = Ticket::find()
            ->where(['LOWER(status_ticket)' => 'open'])
            ->andWhere(['deleted_at' => null]);
        if ($idCustomer !== null && $idCustomer !== '') {
            $queryOpen->andWhere(['customer_id' => $idCustomer]);
        }
        $ticketOpen = $queryOpen->count();

        $queryInProgress = Ticket::find()
            ->where(['LOWER(status_ticket)' => 'in progress'])
            ->andWhere(['deleted_at' => null]);
        if ($idCustomer !== null && $idCustomer !== '') {
            $queryInProgress->andWhere(['customer_id' => $idCustomer]);
        }
        $ticketInProgress = $queryInProgress->count();

        $queryDone = Ticket::find()
            ->where(['LOWER(status_ticket)' => 'done'])
            ->andWhere(['deleted_at' => null]);
        if ($idCustomer !== null && $idCustomer !== '') {
            $queryDone->andWhere(['customer_id' => $idCustomer]);
        }
        $ticketDone = $queryDone->count();

        $queryLate = Ticket::find()
            ->where(['<', 'date_ticket', date('Y-m-d')])
            ->andWhere(['!=', new \yii\db\Expression("LOWER(status_ticket)"), 'done'])
            ->andWhere(['deleted_at' => null]);
        if ($idCustomer !== null  && $idCustomer !== '') {
            $queryLate->andWhere(['customer_id' => $idCustomer]);
        }
        $ticketLate = $queryLate->count();

        $respon = [
            'totalTicket'      => $totalTicket,
            'ticketOpen'       => $ticketOpen,
            'ticketInProgress' => $ticketInProgress,
            'ticketDone'       => $ticketDone,
            'ticketLate'       => $ticketLate,
        ];

        return $respon;
    }


    public static function implementasiFunction($idCustomer)
    {
        $query = (new \yii\db\Query())
            ->select([
                'deals.deals_id',
                'customer.customer_name AS nama_pelanggan',
                'customer.customer_email as email',
                'customer.customer_phone as no_telp',
                'customer.pic_name as kontak_pribadi',
                'product.product_name as nama_produk',
                new \yii\db\Expression("
                COALESCE(NULLIF(impl.status, ''), 'In Progress') AS status
            ")
            ])
            ->from('contract')
            ->innerJoin('pemasukan', 'contract.invoice_id = pemasukan.pemasukan_id')
            ->innerJoin('deals', 'pemasukan.deals_id = deals.deals_id')
            ->innerJoin('customer', 'deals.customer_id = customer.customer_id')
            ->innerJoin('product', 'deals.product_id = product.id_produk')
            ->leftJoin(['impl' => new \yii\db\Expression('
            LATERAL (
                SELECT status
                FROM implementation
                WHERE implementation.deals_id = deals.deals_id
                AND implementation.deleted_at IS NULL
                ORDER BY implementation.id_implementasi DESC
                LIMIT 1
            )
        ')], 'true')
            ->orderBy(['pemasukan.pemasukan_id' => SORT_DESC]);

        if (!empty($idCustomer)) {
            $query->where(['deals.customer_id' => $idCustomer]);
        }


        $allImplementasi = $query->all();

        // Hitung manual
        $totalImplementasi = count($allImplementasi);
        $implementasiOpen = 0;
        $implementasiInProgress = 0;
        $implementasiDone = 0;
        $implementasiLate = 0;


        foreach ($allImplementasi as $key => $value) {
            // $detail = $this->search_implementasi($value['deals_id'], $params);

            $cekdone    = DefaultController::cekStatus('Done', $value['deals_id']);
            $cekopen    = DefaultController::cekStatus('Open', $value['deals_id']);
            $cekprogres = DefaultController::cekStatus('In Progress', $value['deals_id']);

            if (count($cekdone) > 0) {
                if (count($cekopen) > 0 || count($cekprogres) > 0) {
                    $status = "In Progress";
                    $implementasiInProgress++;
                } else {
                    $status = "Done";
                    $implementasiDone++;
                }
            } elseif (count($cekprogres) > 0) {
                $status = "In Progress";
                $implementasiInProgress++;
            } else {
                $status = "Open";
                $implementasiOpen++;
            }

            // $duration = $status === "Done" ? $this->getDurasi($value['deals_id']) : "";

            // if (!empty($this->status) && stripos($status, trim($this->status)) === false) {
            //     continue;
            // }

            // if (!empty($this->duration) && stripos($duration, trim($this->duration)) === false) {
            //     continue;
            // }



        }

        // foreach ($allImplementasi as $impl) {
        //     $status = strtolower(trim($impl['status'] ?? ''));

        //     if ($status === 'open') {
        //         $implementasiOpen++;
        //     } elseif ($status === 'in progress') {
        //         $implementasiInProgress++;
        //     } elseif ($status === 'done') {
        //         $implementasiDone++;
        //     }

        //     if (
        //         isset($impl['completion_date']) &&
        //         $impl['completion_date'] < date('Y-m-d') &&
        //         $status !== 'done'
        //     ) {
        //         $implementasiLate++;
        //     }
        // }

        return [
            'totalImplementasi' => $totalImplementasi,
            'implementasiOpen' => $implementasiOpen,
            'implementasiInProgress' => $implementasiInProgress,
            'implementasiDone' => $implementasiDone,
            'implementasiLate' => $implementasiLate,
        ];



        return $respon;
    }

    public static function cekStatus($status, $deal)
    {
        return (new Query())
            ->from('implementation')
            ->where(['status' => $status])
            ->andWhere(['deals_id' => $deal])
            ->all();
    }



    public function actionOpen()
    {
        $tasks = Task::find()->where(['status' => 'Open'])->all();
        return $this->render('open', ['tasks' => $tasks]);
    }

    public function actionInProgress()
    {
        $tasks = Task::find()->where(['status' => 'In Progress'])->all();
        return $this->render('inprogress', ['tasks' => $tasks]);
    }

    public function actionDone()
    {
        $tasks = Task::find()->where(['status' => 'Done'])->all();
        return $this->render('done', ['tasks' => $tasks]);
    }

    public function actionMerge()
    {
        $tasks = Task::find()->where(['status' => 'Merge'])->all();
        return $this->render('merge', ['tasks' => $tasks]);
    }
}
