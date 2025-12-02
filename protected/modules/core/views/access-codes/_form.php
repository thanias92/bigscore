<?php

use yii\helpers\Html;
use yii\bootstrap4\Alert;
use kartik\form\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\AccessCodes */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="access-codes-form">

    <?php $form = ActiveForm::begin(); ?>
    <?php    if($model->errors){
      Alert::begin([
          'options' => [
              'class' => 'alert-danger',
          ],
      ]);
      echo $form->errorSummary($model);
      Alert::end();
    }
    ?>
    <?= $form->field($model, 'id_access')->textInput() ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'purpose')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'meta')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'is_active')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton('<i class="fas fa-save"></i> Simpan', ['class' => 'btn btn-sm btn-primary']) ?>
        <?= Html::Button('<i class="fas fa-times-circle"></i> Batal', ['class' => 'btn btn-sm btn-danger', 'onclick' => 'history.go(-1)']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
