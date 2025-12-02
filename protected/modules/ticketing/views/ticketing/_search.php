<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\ticketing\TicketingSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="collapse show searchBox" id="ticket-search">
    <div class="ticket-search">

        <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'fieldConfig' => ['options' => ['class' => 'form-group mb-0 mt-0']],
            'method' => 'get',
            'options' => [
                'data-pjax' => 1
            ],
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

        <!-- <div class="form-group d-flex align-items-end flex-column">
      <?= Html::submitButton('<i class="fas fa-search"></i> Cari', ['class' => 'btn btn-warning-info']) ?>
      <?php //Html::resetButton('<i class="fas fa-sync"></i> Reset', ['class' => 'btn btn-soft-warning']) 
        ?>
    </div> -->
        <?php ActiveForm::end(); ?>
    </div>
</div>