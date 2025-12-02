<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\web\View;
use app\widgets\JSRegister;

/* @var $this yii\web\View */
/* @var $model app\models\Ticket */
/* @var $deals app\models\Deals[] */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Ticket', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="ticket-view-as-form">

  <?php $form = ActiveForm::begin([
    'id' => 'form-readonly',
    'type' => ActiveForm::TYPE_VERTICAL,
    'formConfig' => ['deviceSize' => ActiveForm::SIZE_SMALL],
  ]); ?>

  <div class="row">

    <div class="col-md-6">
      <?= $form->field($model, 'code_ticket')->textInput([
        'readonly' => true,
        'class' => 'form-control-sm'
      ]) ?>
    </div>

    <div class="col-md-6">
      <?= $form->field($model, 'id_deals')->widget(Select2::class, [
        'data' => ArrayHelper::map($deals, 'deals_id', function ($d) {
          return $d->customer->customer_name ?? '(Tidak ada nama)';
        }),
        'options' => ['disabled' => true],
        'pluginOptions' => [
          'allowClear' => true,
          // Sesuaikan dropdownParent jika pakai modal
          // 'dropdownParent' => new \yii\web\JsExpression("$('#modal')")
        ],
        'size' => Select2::SMALL,
      ]) ?>
    </div>

    <div class="col-md-6">
      <?= $form->field($model, 'user')->textInput([
        'readonly' => true,
        'class' => 'form-control-sm'
      ]) ?>
    </div>

    <div class="col-md-6">
      <?= Html::label('Email Customer', null, ['class' => 'form-label small']) ?>
      <input type="text" class="form-control form-control-sm" id="customer_email"
             value="<?= Html::encode($model->customer->email ?? '-') ?>" readonly>
    </div>

    <div class="col-md-6">
      <?= $form->field($model, 'date_ticket')->textInput([
        'type' => 'date',
        'readonly' => true,
        'class' => 'form-control-sm'
      ]) ?>
    </div>

        <div class="col-md-6">
      <?= $form->field($model, 'duedate')->textInput([
        'type' => 'date',
        'readonly' => true,
        'class' => 'form-control-sm'
      ]) ?>
    </div>

    <div class="col-md-6">
      <?= $form->field($model, 'priority_ticket')->dropDownList([
        'Low' => 'Low',
        'Medium' => 'Medium',
        'High' => 'High'
      ], [
        'disabled' => true,
        'class' => 'form-control-sm'
      ]) ?>
    </div>

    <div class="col-md-6">
      <?= $form->field($model, 'label_ticket')->textInput([
        'readonly' => true,
        'class' => 'form-control-sm'
      ]) ?>
    </div>

    <div class="col-md-6">
      <?= $form->field($model, 'assigne')->dropDownList([
        'Pasman Rizky' => 'Pasman Rizky',
        'Nanang Sunardi' => 'Nanang Sunardi',
        'Iwan' => 'Iwan'
      ], [
        'disabled' => true,
        'class' => 'form-control-sm'
      ]) ?>
    </div>

    <div class="col-md-6">
      <?= $form->field($model, 'via')->dropDownList([
        'Ticket Mandiri' => 'Ticket Mandiri',
        'Roomchat' => 'Roomchat',
        'Whatsapp' => 'WhatsApp'
      ], [
        'disabled' => true,
        'class' => 'form-control-sm'
      ]) ?>
    </div>

    <div class="col-md-12">
      <?= $form->field($model, 'title')->textInput([
        'readonly' => true,
        'class' => 'form-control-sm'
      ]) ?>
    </div>

    <div class="col-md-12">
      <?= $form->field($model, 'modul')->textInput([
        'readonly' => true,
        'class' => 'form-control-sm'
      ]) ?>
    </div>

    <div class="col-md-12">
      <?= $form->field($model, 'status_ticket')->textInput([
        'readonly' => true,
        'class' => 'form-control-sm'
      ]) ?>
    </div>

    <div class="col-md-12">
      <?= $form->field($model, 'description')->textarea([
        'readonly' => true,
        'rows' => 4,
        'class' => 'form-control-sm'
      ]) ?>
    </div>

  </div>

  <?php ActiveForm::end(); ?>

</div>
