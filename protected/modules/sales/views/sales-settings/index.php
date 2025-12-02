<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Sales Target Settings';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="sales-settings-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>Set the monthly visit target for each salesman.</p>

    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success">
            <?= Yii::$app->session->getFlash('success') ?>
        </div>
    <?php endif; ?>

    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-danger">
            <?= Yii::$app->session->getFlash('error') ?>
        </div>
    <?php endif; ?>

    <?php $form = ActiveForm::begin(); ?>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Salesman</th>
                <th style="width: 200px;">Monthly Visit Target</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($profiles as $index => $profile): ?>
                <tr>
                    <td>
                        <?= Html::encode($profile->user->username) ?>
                        <?= $form->field($profile, "[$index]user_id")->hiddenInput()->label(false) ?>
                    </td>
                    <td>
                        <?= $form->field($profile, "[$index]visit_target")->textInput(['type' => 'number'])->label(false) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="form-group">
        <?= Html::submitButton('Save Changes', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>