<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\AccountKeluar */

$this->title = Yii::t('app', 'Create Account Keluar');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Account Keluar'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="account-keluar-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
