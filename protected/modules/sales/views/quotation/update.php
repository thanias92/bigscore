<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Quotation */

$this->title = 'Update Quotation: ' . $model->quotation_id;
?>
<div class="quotation-update">
    <?= $this->render('_form', [
        'model' => $model,
        'customers' => $customers,
        'products' => $products,
        'mode' => 'edit'
    ]) ?>
</div>