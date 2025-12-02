<?php

namespace app\modules\task\controllers;

use Yii;
use app\models\Task;
use app\models\ItemSubtask;
use app\models\Staff;
use app\models\SubTask;
use app\models\Ticket;
use app\models\Customer;
use app\modules\task\TaskSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\db\Query;

class TaskController extends Controller
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

    // public function actionIndex($status = '')
    // {
    //   // $view = Yii::$app->request->get('view', 'Task'); // Default ke 'Task'
    //   $searchModel = new TaskSearch();
    //   $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    //   $dataProvider->query->andWhere(['ilike', 'status', $status]);

    //   $model = new Task();

    //   // Render tampilan berdasarkan 'view' yang dipilih
    //   return $this->render('index', [
    //     'model' => $model,
    //     'searchModel' => $searchModel,
    //     'dataProvider' => $dataProvider,
    //     // 'view' => $view, // Menyediakan data view untuk render
    //   ]);
    // }

    public function actionIndex($status = '')
    {
        $this->importWaitingTicketsToTask();
        // $view = Yii::$app->request->get('view', 'Task'); // Default ke 'Task'
        $searchModel  = new TaskSearch();
        $statusParam  = Yii::$app->request->get('status', 'waiting');
        $queryString  = Yii::$app->request->get('queryString', null);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query
            ->andWhere(['ilike', 'status', $statusParam])
            ->andFilterWhere(['ilike', 'title', $queryString])
            ->orderBy(['id_task' => SORT_DESC]);

        $viewMode   = Yii::$app->request->get('viewMode', 'task');
        $statusView = $statusParam;
        // $queryString = 
        $model      = new Task();

        // Render tampilan berdasarkan 'view' yang dipilih
        $jumlahTask = $this->getJumlahTaskInProgress();
        if ($viewMode == 'board') {
            $openDataProvider = new ActiveDataProvider([
                'query' => Task::find()
                    ->where(['status' => 'Open'])
                    ->andWhere(['deleted_at' => null]),
                'pagination' => ['pageSize' => 10],
            ]);

            $progressDataProvider = new ActiveDataProvider([
                'query' => Task::find()
                    ->where(['status' => 'In Progress'])
                    ->andWhere(['deleted_at' => null]),
                'pagination' => ['pageSize' => 10],
            ]);

            $doneDataProvider = new ActiveDataProvider([
                'query' => Task::find()
                    ->where(['status' => 'Done'])
                    ->andWhere(['deleted_at' => null]),
                'pagination' => ['pageSize' => 10],
            ]);

            $mergeDataProvider = new ActiveDataProvider([
                'query' => Task::find()
                    ->where(['status' => 'Merge'])
                    ->andWhere(['deleted_at' => null]),
                'pagination' => ['pageSize' => 10],
            ]);



            return $this->render('board_progress', [
                'openDataProvider' => $openDataProvider,
                'progressDataProvider' => $progressDataProvider,
                'doneDataProvider' => $doneDataProvider,
                'mergeDataProvider' => $mergeDataProvider,
                'searchModel' => $searchModel,
                'viewMode' => $viewMode,
                'jumlahTask' => $jumlahTask
            ]);
        } elseif ($viewMode == 'table') {
            return $this->render('table_view', [
                'model' => $model,
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'viewMode' => $viewMode,
                'statusView' => $statusView,
                'queryString' => $queryString,
                'jumlahTask' => $jumlahTask
            ]);
        } else {
            // Default 'task' view
            return $this->render('index', [
                'model' => $model,
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'statusView' => $statusView,
                'queryString' => $queryString,
                'jumlahTask' => $jumlahTask
            ]);
        }
    }

    //buat limit task in progress
    public function getJumlahTaskInProgress()
    {
        $task = Task::find()->where(['status' => 'In Progress'])->count();
        if ($task > 10) {
            return true;
        }
        return false;
    }

    //ambil ticket statusnya waiting
    public function importWaitingTicketsToTask()
    {
        $waitingTickets = Ticket::find()
            ->where(['LOWER(status_ticket)'=> 'waiting'])
            ->andWhere(['role' => 'staff'])
            ->all();

        foreach ($waitingTickets as $ticket) {
            $taskExists = \app\models\Task::find()
                ->where(['id_ticket' => $ticket->id_ticket])
                ->exists();

            if ($taskExists) {
                continue;
            }

            $cekDeals = (new Query())
                ->select('*')
                ->from('deals')
                ->where(['deals_id' => $ticket->id_deals])
                ->limit(1)
                ->one();


            $cekCustomer = (new Query())
                ->select('*')
                ->from('customer')
                ->where(['customer_id' => $cekDeals['customer_id']])
                ->limit(1)
                ->one();

            $task = new \app\models\Task();
            $task->id_ticket      = $ticket->id_ticket;
            $task->title          = $ticket->title;
            $task->modul          = $ticket->modul;
            $task->priority_task  = $ticket->priority_ticket;
            $task->customer_id    = $cekCustomer['customer_id'];
            $task->assign         = $ticket->assigne;
            $task->status         = 'waiting';
            $task->description    = $ticket->description;
            $task->label_task = [$ticket->label_ticket];
            $task->duedate_task   = $ticket->date_ticket;
            $task->finishdate_task = null;

            if (!$task->save()) {
                Yii::error("Failed saving task from ticket {$ticket->id_ticket}: " . json_encode($task->errors));
            }
        }
    }

    //tambah task
    public function actionCreate()
    {
        $model = new Task();
        $assignList = \yii\helpers\ArrayHelper::map(
            Staff::find()->orderBy('nama_lengkap')->all(),
            'nama_lengkap',
            'nama_lengkap'
        );

        $customers = ArrayHelper::map(
            Customer::find()->orderBy('customer_name')->all(),
            'customer_id',
            'customer_name'
        );

        $username = Yii::$app->user->identity->username;
        if (!array_key_exists($username, $assignList)) {
            $assignList[$username] = $username . ' (You)';
        }

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {

                $post = Yii::$app->request->post();

                if (!empty($post['sub_tasks'])) {
                    foreach ($post['sub_tasks'] as $index => $value) {
                        $modelSubTask = new SubTask();
                        $modelSubTask->id_task = $model->id_task;
                        $modelSubTask->title_subtask = $value['title_subtask'];
                        $modelSubTask->progress_subtask = '100';
                        $modelSubTask->created_by = Yii::$app->user->id;
                        $modelSubTask->created_at = date('Y-m-d H:i:s');

                        if ($modelSubTask->save()) {
                            if (!empty($value['item_subtasks'])) {
                                foreach ($value['item_subtasks'] as $itemData) {
                                    $item = new ItemSubtask();
                                    $item->id_subtask = $modelSubTask->id_subtask;
                                    $item->item_subtask = $itemData['value'] ?? '';
                                    $item->status = isset($itemData['status']) ? 1 : 0;
                                    $item->created_by = Yii::$app->user->id;
                                    $item->created_at = date('Y-m-d H:i:s');
                                    $item->save();
                                }
                            }
                        }
                    }
                }

                Yii::$app->session->setFlash('success', 'Berhasil Menambah Data');
                $returnUrl = Yii::$app->request->post('returnUrl', ['index']);
                return $this->redirect($returnUrl);
                // return $this->redirect(['index']);
            } else {
                Yii::$app->session->setFlash('error', 'Gagal Menambah Data');
            }
        }

        $jumlahTask = $this->getJumlahTaskInProgress();

        $returnUrl = Yii::$app->request->get('returnUrl', Yii::$app->request->referrer);
        return $this->renderAjax('create', [
            'model' => $model,
            'assignList' => $assignList,
            'customers' => $customers,
            'returnUrl' => $returnUrl,
            'jumlahTask' => $jumlahTask,
        ]);
    }


    public function actionView($id_task)
    {
        $model = $this->findModel($id_task);

        $assignList = \yii\helpers\ArrayHelper::map(
            Staff::find()->orderBy('nama_lengkap')->all(),
            'nama_lengkap',
            'nama_lengkap'
        );

        $jumlahTask = $this->getJumlahTaskInProgress();

        $username = Yii::$app->user->identity->username;
        if (!array_key_exists($username, $assignList)) {
            $assignList[$username] = $username . ' (You)';
        }

        $customers = ArrayHelper::map(
            Customer::find()->orderBy('customer_name')->all(),
            'customer_id',
            'customer_name'
        );

        if (Yii::$app->request->isAjax) {
            $subTasks = SubTask::find()
                ->where(['id_task' => $model->id_task])
                ->with('itemSubtasks')
                ->asArray()
                ->all();
            $returnUrl = Yii::$app->request->get('returnUrl', Yii::$app->request->referrer);
            return $this->renderAjax('_form', [
                'model' => $model,
                'assignList' => $assignList,
                'subTasks' => $subTasks,
                'customers' => $customers,
                'returnUrl' => $returnUrl,
                'jumlahTask' => $jumlahTask,
            ]);
        }

        return $this->render('view', [
            'model' => $model
        ]);
    }



    public function actionUpdate($id_task)
    {
        $model = $this->findModel($id_task);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {

            $post = Yii::$app->request->post();

            if ($model->id_ticket !== null) {
                $ticket = Ticket::findOne($model->id_ticket);
                if ($ticket !== null) {
                    $newStatus = $post['Task']['status'] ?? $model->status ?? 'Open';
                    if ($ticket->status_ticket !== $newStatus) {
                        $ticket->updateAttributes(['status_ticket' => $newStatus]);
                    }
                }
            }


            $oldSubTasks = SubTask::find()->where(['id_task' => $model->id_task])->all();
            foreach ($oldSubTasks as $oldSubTask) {
                // Hapus semua item subtask yang terkait
                ItemSubtask::deleteAll(['id_subtask' => $oldSubTask->id_subtask]);
            }

            // Hapus semua subtask
            SubTask::deleteAll(['id_task' => $model->id_task]);

            if (!empty($post['sub_tasks'])) {
                foreach ($post['sub_tasks'] as $index => $subtaskData) {
                    $modelSubTask = new SubTask();
                    $modelSubTask->id_task = $model->id_task;
                    $modelSubTask->title_subtask = $subtaskData['title_subtask'] ?? null;
                    $modelSubTask->progress_subtask = '100';
                    $modelSubTask->created_by = Yii::$app->user->id;
                    $modelSubTask->created_at = date('Y-m-d H:i:s');

                    if ($modelSubTask->save()) {
                        // Simpan item subtask
                        if (!empty($subtaskData['item_subtasks'])) {
                            foreach ($subtaskData['item_subtasks'] as $itemData) {
                                $item = new ItemSubtask();
                                $item->id_subtask = $modelSubTask->id_subtask;
                                $item->item_subtask = $itemData['value'] ?? '';
                                $item->status = isset($itemData['status']) ? 1 : 0;
                                $item->created_by = Yii::$app->user->id;
                                $item->created_at = date('Y-m-d H:i:s');
                                $item->save();
                            }
                        }
                    }
                }
            }

            Yii::$app->session->setFlash('success', 'Tugas berhasil diperbarui.');
            $returnUrl = Yii::$app->request->post('returnUrl', ['index']);
            return $this->redirect($returnUrl);
            // return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }



    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $subTasks = SubTask::find()->where(['id_task' => $model->id_task])->all();
        foreach ($subTasks as $subTask) {
            ItemSubtask::deleteAll(['id_subtask' => $subTask->id_subtask]);
            $subTask->delete();
        }
        $model->delete();

        Yii::$app->session->setFlash('success', 'Data berhasil dihapus.');

        $uie = "asa";

        $returnUrl = Yii::$app->request->get('returnUrl');
        return $this->redirect($returnUrl ?: ['index']);
    }



    protected function findModel($id_task)
    {
        if (($model = Task::findOne(['id_task' => $id_task])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
