<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Task */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Task', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="task-view">
  <?= DetailView::widget([
  'model' => $model,
  'options' => ['class' => 'table table-hover table-sm'],
  'attributes' => [
            // 'id_task',
            'title',
            'label_task',
            'modul',
            'priority_task',
            'assign',
            'status',
            // 'duedate_task',
            // 'finishdate_task',
            'description:ntext',
            // 'created_by',
            // 'updated_by',
            // 'deleted_by',
            // 'created_at',
            // 'updated_at',
            // 'deleted_at',
  ],
  ]) ?>

</div>
