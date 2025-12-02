<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Pengeluaran */

$this->title = Yii::t('app', 'Create Pengeluaran');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Pengeluaran'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pengeluaran-create">
    <?= $this->render('_form', [
        'model' => $model,
        'listVendor' => $listVendor,
    ]) ?>
</div>
