<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Laporan */

$this->title = Yii::t('app', 'Update Laporan: {name}', [
    'name' => $model->laporan_id,
]);
?>
<div class="laporan-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
