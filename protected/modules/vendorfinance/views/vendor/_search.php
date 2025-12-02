<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\vendorfinance\VendorSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="collapse show searchBox" id="vendor-search">
  <div class="vendor-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'fieldConfig' => ['options' => ['class' => 'form-group mb-0 mt-0']],
    'method' => 'get',
        ]); ?>
    <div class="row">
    <?= $form->field($model, 'queryString', [
    'addon' => [
        'append' => [
            'content' => Html::button('Cari', ['class'=>'btn btn-light']),
            'asButton' => true
        ]
    ]
])->textInput([
    'id' => 'search-vendor', 
    'placeholder'=>'Pencarian ...' 
])->label(false); ?>
    </div>

      <?= Html::submitButton('<i class="fas fa-search"></i> Cari', ['class' => 'btn btn-warning-info']) ?>
      <?php //Html::resetButton('<i class="fas fa-sync"></i> Reset', ['class' => 'btn btn-soft-warning']) ?>
    </div> -->
    <?php ActiveForm::end(); ?>
  </div>
</div>
