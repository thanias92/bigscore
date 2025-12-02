<?php

use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;


/* @var $this yii\web\View */
/* @var $model app\models\Menu */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="menu-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'parent')->widget(Select2::classname(), [
        'data' => app\modules\core\controllers\MenuController::getParent($model->id),
        'size' => Select2::SMALL,
        'options' => ['placeholder' => 'Pilih', 'options'=>[$model->parent=>["Selected"=>true]]],
        'pluginOptions' => ['allowClear' => true,],
      ]);
    ?>

    <?= $form->field($model, 'route')->widget(Select2::classname(), [
        'data' => app\modules\core\controllers\MenuController::getRouteReference(),
        'size' => Select2::SMALL,
        'options' => ['placeholder' => 'Pilih', 'options'=>[$model->route=>["Selected"=>true]]],
        'pluginOptions' => ['allowClear' => true,],
      ]);
    ?>
    <?php
      $display = 'none';
      if(!$model->isNewRecord) {
        if(!empty($model->route)){
          $display="block";
        }
      } else {
        if (!empty($model->route)) {
            $display = "block";
        }
      }
    ?>
    <div id="params" style="display:<?=$display;?>">
      <button
        class="m-portlet__nav-link btn btn-sm btn-brand m-btn m-btn--pill m-btn--air"
        id="addParams"
      >
        <i class="fa fa-plus"></i>
        &nbsp;
        Tambah Parameter
      </button>
      <br>
      <br>
      <table class="table">
        <thead>
          <tr>
            <th>Nama Parameter</th>
            <th>Nilai Parameter</th>
            <th>#</th>
          </tr>
        </thead>
        <tbody id="params-input">
          <?php
          if (!$model->isNewRecord) {
            if (!empty($model->params)) {
                $params = Json::decode($model->params, $asArray = true);
                $index = 0;
                if(!empty($params)) {
                  foreach ($params as $key => $value) {
                    echo '<tr id="row' . $index . '"><td><input name="Menu[params][' . $index . '][key]" class="form-control" placeholder="Nama Parameter" required value="' . $key . '"/></td><td><input name="Menu[params][' . $index . '][value]" class="form-control" placeholder="Nilai Parameter" required value="' . $value . '" /></td><td><button class="btn btn-sm btn-danger" id="delete" data="' . $index . '"><i class="fa fa-times"></i>X</button></td></tr>';
                    $index++;
                  }
                }
            } else {
              $index = 0;
            }
          } else {
            $index = 0;
          }
          ?>
        </tbody>
      </table>
      <hr >
    </div>

    <?= $form->field($model, 'data')->textarea(['row' => 6]) ?>
    <?= $form->field($model, 'deskripsi')->textarea(['row' => 9]) ?>

    <div class="form-group">
        <?= Html::submitButton('<i class="fa fa-save"></i> Simpan', ['class' => 'btn btn-sm btn-primary']) ?>
        <?= Html::Button('<i class="fa fa-times-circle"></i> Batal', ['class' => 'btn btn-sm btn-danger', 'onclick' => 'history.go(-1)']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


<?php
$this->registerJs(<<<JS
  var inputIndex = $index;
  $(document).on("click","#delete",function() {
    var id = $(this).attr('data');
    $("#row"+id).remove();
  });

  $("#menu-route").change(function(){
    var url = $("#menu-route").val();
    if(!url) {
      $("#params").hide();
    } else {
      $("#params").show();
    }
  });

  $("#addParams").click(function(){
    $("#params-input").append('<tr id="row'+inputIndex+'"><td><input name="Menu[params]['+inputIndex+'][key]" class="form-control" placeholder="Nama Parameter" required /></td><td><input name="Menu[params]['+inputIndex+'][value]" class="form-control" placeholder="Nilai Parameter" required /></td><td><button class="btn btn-sm btn-danger" id="delete" data="'+inputIndex+'"><i class="fa fa-times"></i>X</button></td></tr>');
    inputIndex++;
  });
JS
);
?>
