<?php

namespace app\modules\task\controllers;

use yii\web\Controller;
use app\models\Task; // Asumsi Anda memiliki model Task

class OpenController extends Controller
{
  public function actionIndex()
  {
    $tasks = Task::find()->all(); // Atau logika pengambilan task awal Anda
    $model = new \app\models\Task(); // Membuat instance model Task untuk form

    return $this->render('open', [
      'tasks' => $tasks, // Pastikan Anda mengirimkan data task juga jika diperlukan di halaman utama
      'model' => $model, // Mengirim instance model ke view
    ]);
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
