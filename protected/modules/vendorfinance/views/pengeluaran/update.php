<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Pengeluaran */

$this->title = Yii::t('app', 'Update Pengeluaran: {name}', [
    'name' => $model->id_pengeluaran,
]);
?>
<div class="pengeluaran-update">
    <?= $this->render('_form', [
        'model' => $model,
        'listVendor' => $listVendor,
        'listAccount' => $listAccount,
    ]) ?>
</div>