<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Vendor */

$this->title = 'Update Vendor: ' . $model->id_vendor;
?>
<div class="vendor-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>

