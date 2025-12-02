<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Implementation *///kanza push ulang untuk hosting

$this->title = 'Create Implementation';
$this->params['breadcrumbs'][] = ['label' => 'Implementation', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="implementation-create">
    <?= $this->render('_form', compact('model', 'statusProgress', 'detailList')) ?>
</div>