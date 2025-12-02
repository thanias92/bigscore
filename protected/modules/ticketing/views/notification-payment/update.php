<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\NotificationPayment */

$this->title = 'Update Notification Payment: ' . $model->id_notification;
?>
<div class="notification-payment-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
