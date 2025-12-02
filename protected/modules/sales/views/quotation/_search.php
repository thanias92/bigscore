<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\sales\QuotationSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="collapse show searchBox" id="quotation-search">
  <div class="quotation-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'fieldConfig' => ['options' => ['class' => 'form-group mb-0 mt-0']],
    'method' => 'get',
        ]); ?>
    <div class="row">
      <?php      
      echo $form->field($model, 'queryString', [
      'addClass' => 'form-control',
      'addon' => [
      'append' => [
      'content' => Html::button('Cari |
      <svg class="icon-16" width="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="11.7669" cy="11.7666" r="8.98856" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></circle>
        <path d="M18.0186 18.4851L21.5426 22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
      </svg>', ['class'=>'btn btn-secondary']),
      'asButton' => true
      ]
      ]
      ])->textInput(['placeholder'=>'Pencarian ...'])->label(false);
      ?>      
      <div class="col-4">
    <?php // echo $form->field($model, 'quotation_id') ?>
</div>
<div class="col-4">
    <?php // echo $form->field($model, 'customer_id') ?>
</div>
<div class="col-4">
    <?php // echo $form->field($model, 'product_id') ?>
</div>
<div class="col-4">
    <?php // echo $form->field($model, 'quotation_code') ?>
</div>
<div class="col-4">
    <?php // echo $form->field($model, 'unit_product') ?>
</div>
<div class="col-4">
    <?php // echo $form->field($model, 'price_product') ?>
</div>
<div class="col-4">
    <?php // echo $form->field($model, 'total') ?>
</div>
<div class="col-4">
    <?php // echo $form->field($model, 'quotation_file') ?>
</div>
<div class="col-4">
    <?php // echo $form->field($model, 'created_date') ?>
</div>
<div class="col-4">
    <?php // echo $form->field($model, 'expiration_date') ?>
</div>
<div class="col-4">
    <?php // echo $form->field($model, 'description') ?>
</div>
<div class="col-4">
    <?php // echo $form->field($model, 'created_by') ?>
</div>
<div class="col-4">
    <?php // echo $form->field($model, 'updated_by') ?>
</div>
<div class="col-4">
    <?php // echo $form->field($model, 'deleted_by') ?>
</div>
<div class="col-4">
    <?php // echo $form->field($model, 'created_at') ?>
</div>
<div class="col-4">
    <?php // echo $form->field($model, 'updated_at') ?>
</div>
<div class="col-4">
    <?php // echo $form->field($model, 'deleted_at') ?>
</div>
    </div>

    <!-- <div class="form-group d-flex align-items-end flex-column">
      <?= Html::submitButton('<i class="fas fa-search"></i> Cari', ['class' => 'btn btn-warning-info']) ?>
      <?php //Html::resetButton('<i class="fas fa-sync"></i> Reset', ['class' => 'btn btn-soft-warning']) ?>
    </div> -->
    <?php ActiveForm::end(); ?>
  </div>
</div>