<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Customer */

$this->title = 'Update Customer: ' . $model->customer_id;
?>
<div class="customer-update">
    <?= $this->render('_form', [
        'model' => $model,
        'mode' => 'edit'
    ]) ?>
</div>
