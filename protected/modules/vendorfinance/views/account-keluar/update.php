<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\AccountKeluar */

$this->title = Yii::t('app', 'Update Account Keluar: {name}', [
    'name' => $model->id,
]);
?>
<div class="account-keluar-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
