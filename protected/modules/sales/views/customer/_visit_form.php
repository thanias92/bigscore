<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var app\models\CustomerVisit $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="customer-visit-form">

    <?php $form = ActiveForm::begin(['id' => 'visit-form']); ?>

    <?= $form->field($model, 'customer_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'visit_date')->input('date', ['class' => 'form-control']) ?>

    <?= $form->field($model, 'notes')->textarea(['rows' => 4, 'placeholder' => 'Contoh: Diskusi mengenai penawaran produk X, klien meminta demo minggu depan...']) ?>

    <?php ActiveForm::end(); ?>

</div>