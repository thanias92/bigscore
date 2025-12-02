<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\vendorfinance\ProductSearch */
?>

<div class="collapse show searchBox" id="product-search">
  <div class="product-search">

    <?php $form = ActiveForm::begin([
      'action' => ['index'],
      'fieldConfig' => ['options' => ['class' => 'form-group mb-0 mt-0']],
      'method' => 'get',
      'options' => ['data-pjax' => 0], // <== Jangan submit via pjax langsung
    ]); ?>

    <div class="row">
      <div class="col-md-12">
        <?= $form->field($model, 'queryString', [
          'addon' => [
            'append' => [
              'content' => '<span class="input-group-text"><i class="fas fa-search"></i></span>',
              'asButton' => false
            ]
          ]
        ])->textInput([
          'id' => 'search-input',
          'placeholder' => 'Cari produk...',
          'autocomplete' => 'off'
        ])->label(false); ?>
      </div>
    </div>

    <?php ActiveForm::end(); ?>
  </div>
</div>
