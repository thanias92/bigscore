<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Contract */

$this->title = 'Create Contract';
$this->params['breadcrumbs'][] = ['label' => 'Contract', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contract-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
