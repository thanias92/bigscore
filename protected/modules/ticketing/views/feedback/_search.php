<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\ticketing\FeedbackSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="collapse show searchBox" id="feedback-search">
  <div class="feedback-search">

    <?php $form = ActiveForm::begin([
      'action' => ['index'],
      'fieldConfig' => ['options' => ['class' => 'form-group mb-0 mt-0']],
      'method' => 'get',
      'options' => ['data-pjax' => 1],
    ]); ?>

   <div class="row">
            <?php
            echo $form->field($model, 'queryString', [
                'addClass' => 'form-control',
                'addon' => [
                    'append' => [
                        'content' => Html::submitButton('Cari', ['class' => 'btn btn-light']),
                        'asButton' => true
                    ]
                ]
            ])->textInput(['placeholder' => 'Search ...'])->label(false);
            ?>
        </div>

    <?php ActiveForm::end(); ?>
  </div>
</div>
