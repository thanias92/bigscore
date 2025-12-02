<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\NotificationPayment */

$this->title = 'Create Notification Payment';
$this->params['breadcrumbs'][] = ['label' => 'Notification Payment', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="notification-payment-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
