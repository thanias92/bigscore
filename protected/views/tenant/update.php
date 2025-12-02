<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Tenant */

$this->title = 'Update Tenant: ' . $model->name;
?>
<div class="tenant-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
