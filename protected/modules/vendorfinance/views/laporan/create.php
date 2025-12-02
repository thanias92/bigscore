<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Laporan */

$this->title = Yii::t('app', 'Create Laporan');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Laporan'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="laporan-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
