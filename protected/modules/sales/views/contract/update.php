<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Contract */

$this->title = 'Update Contract: ' . $model->contract_id;
?>
<div class="contract-update">
    <?= $this->render('_form', [
        'model' => $model,
        'pemasukans' => $pemasukans,
        'mode' => 'edit'
    ]) ?>
</div>
