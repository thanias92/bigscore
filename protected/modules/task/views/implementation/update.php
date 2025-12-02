<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Implementation */
//kanza push ulang untuk hosting
// $this->title = 'Update Implementation: ' . $model->id_implementasi;
?>
<div class="implementation-update">
    <?= $this->render('_form', compact('model', 'statusProgress', 'detailList')) ?>
</div>