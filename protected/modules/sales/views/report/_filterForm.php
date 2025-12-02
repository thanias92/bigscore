<?php

use kartik\date\DatePicker;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
?>

<div class="report-filter-form mb-4 p-3 border rounded">
    <?php $form = ActiveForm::begin([
        'action' => [$this->context->action->id],
        'method' => 'get',
    ]); ?>
    <div class="row">
        <div class="col-md-5">
            <?= $form->field($model, 'startDate')->widget(DatePicker::class, [
                'pluginOptions' => ['format' => 'yyyy-mm-dd', 'autoclose' => true]
            ]) ?>
        </div>
        <div class="col-md-5">
            <?= $form->field($model, 'endDate')->widget(DatePicker::class, [
                'pluginOptions' => ['format' => 'yyyy-mm-dd', 'autoclose' => true]
            ]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-5">
            <?= Html::submitButton('Terapkan', ['class' => 'btn btn-primary w-100']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>