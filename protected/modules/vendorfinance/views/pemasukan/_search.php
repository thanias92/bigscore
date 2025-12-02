<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\vendorfinance\PemasukanSearch */

?>

<div class="collapse show searchBox" id="pemasukan-search">
  <div class="pemasukan-search">

    <?php $form = ActiveForm::begin([
      'action' => ['index'],
      'method' => 'get',
      'fieldConfig' => ['options' => ['class' => 'form-group mb-0 mt-0']],
      'options' => [
        // 'data-pjax' => 1,
      ],
    ]); ?>

    <div class="row">
      <?= $form->field($model, 'queryString', [
        'addon' => [
          'append' => [
            'content' => Html::button('Cari', ['class' => 'btn btn-light']),
            'asButton' => true
          ]
        ]
      ])->textInput([
        'id' => 'search-input',
        'placeholder' => 'Cari data Pemasukan'
      ])->label(false); ?>
    </div>

    <?php ActiveForm::end(); ?>

  </div>
</div>
