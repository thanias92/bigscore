<?php

use yii\helpers\Html;
use yii\helpers\Url;
use mdm\admin\components\Helper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\components\MenuHelper;
use app\models\Menu;
use yii\bootstrap\Modal;
use yii\widgets\ActiveForm;
use kartik\builder\Form;

$this->registerCssFile('@web/themes/vendor/jquery-nestable/jquery.nestable.css');
$this->registerJsFile('@web/themes/vendor/jquery-nestable/jquery.nestable.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$this->title = 'Menus';
$this->params['breadcrumbs'][] = $this->title;
$this->params['pageIcon'] = 'fa fa-th-large';

echo Helper::checkRoute('create') ? Html::a('<i class="fa fa-plus"></i> Tambah', ['create'], ['class' => 'btn btn-primary']) : null;
?>

<div class="menu-index">
  <div class="dd" id="nestable_list_1">
    <button
      id="nestable_list_menu"
      data-action="collapse-all"
      class="btn btn-sm btn-primary"
    >
      <i class="fa fa-minus"></i>
      &nbsp;
      Collapse Menu
    </button>
    <br/>
    <br/>
    <?php
    $menu_items = Menu::find()->orderBy(['order'=>SORT_ASC])->all();
    $data = MenuHelper::MenuManager($menu_items);
    echo $data;
    ?>
  </div>
  <hr />
  <?php $form = ActiveForm::begin();
  echo $form->field($model, 'json_tree')->hiddenInput(['id'=>'nestable_list_1_output'])->label(false);

  echo Html::submitButton('<i class="fa fa-save"></i> Simpan Perubahan', ['class' => 'btn btn-sm btn-primary']);
  ActiveForm::end();
  ?>
  <div style="clear:both"></div>
</div>

<?php
$this->registerJs(<<<JS
    var UINestable = function () {
    var updateOutput = function (e) {
        var list = e.length ? e : $(e.target),
            output = list.data('output');
        if (window.JSON) {
            output.val(window.JSON.stringify(list.nestable('serialize'))); //, null, 2));
        } else {
            output.val('JSON browser support required for this demo.');
        }
    };
    $('#nestable_list_menu').on('click', function () {
      var action = $(this).attr('data-action');
      console.log(action);
        if (action === 'expand-all') {
            $('.dd').nestable('expandAll');
            $(this).attr('data-action','collapse-all');
            $(this).html('<i class="fa fa-minus"></i>&nbsp;Collapse Menu');
        }
        if (action === 'collapse-all') {
            $('.dd').nestable('collapseAll');
            $(this).attr('data-action','expand-all');
            $(this).html('<i class="fa fa-expand"></i>&nbsp;Expand Menu');
        }
    });
    return {
        //main function to initiate the module
        init: function () {
            // activate Nestable for list 1
            $('#nestable_list_1').nestable({
                maxDepth : 10
            })
                .on('change', updateOutput);
            // output initial serialised data
            updateOutput($('#nestable_list_1').data('output', $('#nestable_list_1_output')));
        }
    };
}();

UINestable.init();
JS
);
?>
