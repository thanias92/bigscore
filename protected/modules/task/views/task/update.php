<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Task */

$this->title = 'Update Task: ' . $model->title;
?>
<div class="task-update">
    <?= $this->render('_form', [
        'model' => $model,
        'assignList' => $assignList,
        'subTasks' => $subTasks
    ]) ?>
</div>
