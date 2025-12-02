<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Deals */

$this->title = 'Update Deals: ' . $model->deals_id;
?>
<div class="deals-update">
    <?= $this->render('_form', [
        'model' => $model,
        'customers' => $customers,
        'products' => $products,
        'mode' => 'edit'
    ]) ?>
</div>