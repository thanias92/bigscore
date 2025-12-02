<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\AccessCodesSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="collapse searchBox" id="access-codes-search" style="">
  <div class="access-codes-search">

      <?php $form = ActiveForm::begin([
          'action' => ['index'],
          'method' => 'get',
        ]); ?>

      <?= $form->field($model, 'id_access') ?>

    <?= $form->field($model, 'username') ?>

    <?= $form->field($model, 'password') ?>

    <?= $form->field($model, 'purpose') ?>

    <?= $form->field($model, 'meta') ?>

    <?php // echo $form->field($model, 'is_active')->checkbox() ?>

      <div class="form-group">
          <?= Html::submitButton('Cari |
                                <svg class="icon-16" width="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="11.7669" cy="11.7666" r="8.98856" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></circle>
                                    <path d="M18.0186 18.4851L21.5426 22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>', ['class' => 'btn btn-primary btn-sm btn-icon']) ?>
          <?= Html::resetButton('<i class="fas fa-sync"></i> Reset', ['class' => 'btn btn-warning btn-sm btn-icon']) ?>
      </div>
      <?php ActiveForm::end(); ?>
  </div>
</div>
