<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Pemasukan */

$this->title = Yii::t('app', '');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Pemasukan'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pemasukan-create">

    <h4><?= Html::encode($this->title) ?></h4>

    <?= $this->render('_form', [
    'model' => $model,
    'akunPemasukanList' => $akunPemasukanList,
]) ?>

</div>
