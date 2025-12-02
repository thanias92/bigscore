<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\vendorfinance\PengeluaranSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="collapse show searchBox" id="pengeluaran-search">
  <div class="pengeluaran-search">

    <?php $form = ActiveForm::begin([
      'method' => 'get',
      'action' => ['index'],
      'options' => ['data-pjax' => 1]
    ]); ?>

    <?= Html::textInput('PengeluaranSearch[queryString]', $model->queryString, [
      'class' => 'form-control',
      'placeholder' => 'Cari data Pengeluaran',
      'id' => 'search-input', // penting agar cocok dengan JS
      'data-filter' => 'pengeluaran'
    ]) ?>

    <?php ActiveForm::end(); ?>

  </div>
</div>
