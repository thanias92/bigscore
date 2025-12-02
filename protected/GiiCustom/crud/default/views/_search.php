<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

echo "<?php\n";
?>

use yii\helpers\Html;
use kartik\form\ActiveForm;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->searchModelClass, '\\') ?> */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="collapse show searchBox" id="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-search">
  <div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-search">

    <?= "<?php " ?>$form = ActiveForm::begin([
    'action' => ['index'],
    'fieldConfig' => ['options' => ['class' => 'form-group mb-0 mt-0']],
    'method' => 'get',
    <?php if ($generator->enablePjax) : ?>
      'options' => [
      'data-pjax' => 1
      ],
    <?php endif; ?>
    ]); ?>
    <div class="row">
      <?= '<?php' ?>
      <?= "\n" ?>
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
      <?= '?>' ?>
      <?= "\n" ?>
      <?php
      $count = 0;
      foreach ($generator->getColumnNames() as $attribute) {
        echo '<div class="col-4">';
        echo "\n";
        if (++$count < 6) {
          echo "    <?php // echo " . $generator->generateActiveSearchField($attribute) . " ?>\n";
        } else {
          echo "    <?php // echo " . $generator->generateActiveSearchField($attribute) . " ?>\n";
        }

        echo '</div>';
        echo "\n";
      }
      ?>
    </div>

    <!-- <div class="form-group d-flex align-items-end flex-column">
      <?= "<?= " ?>Html::submitButton('<i class="fas fa-search"></i> Cari', ['class' => 'btn btn-warning-info']) ?>
      <?= "<?php //" ?>Html::resetButton('<i class="fas fa-sync"></i> Reset', ['class' => 'btn btn-soft-warning']) ?>
    </div> -->
    <?= "<?php " ?>ActiveForm::end(); ?>
  </div>
</div>