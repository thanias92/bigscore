<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Ticket */

$this->title = 'Update Ticket: ' . $model->title;
?>
<div class="ticket-update">
    <?= $this->render('_form', [
        'model' => $model,
        'deals' => $deals,
    ]) ?>
</div>