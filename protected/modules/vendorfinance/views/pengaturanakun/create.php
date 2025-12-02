<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Pengaturanakun */

$this->title = Yii::t('app', 'Create Pengaturanakun');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Pengaturanakun'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pengaturanakun-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
