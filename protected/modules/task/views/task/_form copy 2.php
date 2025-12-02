<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Task;
use dosamigos\ckeditor\CKEditor;
use yii\helpers\Json;

use kartik\select2\Select2;


$this->registerJs("
    var subTasks = " . Json::encode($subTasks ?? []) . ";
  ", \yii\web\View::POS_END);



?>
<div class="task-form">
  <?php $form = ActiveForm::begin([
    'id' => 'task-form',
    'action' => $model->isNewRecord ? ['create'] : ['update', 'id_task' => $model->id_task],
    'method' => 'post',
  ]); ?>

  <?= $form->field($model, 'title')->textInput(['maxlength' => true, 'required' => true]) ?>

  <?= $form->field($model, 'customer_id')->widget(Select2::class, [
    'data' => $customers,
    'options' => [
      'placeholder' => '-- Pilih Customer --',
      'required' => true,
    ],
    'pluginOptions' => [
      'allowClear' => true,
    ],
  ]) ?>

  <div class="row">
    <div class="col-md-6">
      <?= $form->field($model, 'label_task')->widget(Select2::class, [
        'data' => ['Fitur' => 'Fitur', 'Bug' => 'Bug', 'Dokumentasi' => 'Dokumentasi', 'Feature Request' => 'Feature Request'],
        'options' => [
          'placeholder' => 'Ketik atau pilih label...',
          'multiple' => true,
          'tags' => true,
        ],
        'pluginOptions' => [
          'allowClear' => true,
          'tokenSeparators' => [',', ' '],
        ],
      ]);
      ?>

    </div>

    <div class="col-md-6">
      <?= $form->field($model, 'modul')->textInput(['required' => true])->dropDownList(['Pendaftaran' => 'Pendaftaran', 'Pengguna' => 'Pengguna', 'Laporan' => 'Laporan'], ['options' => [$model->modul => ['Selected' => true]]]) ?>
    </div>
  </div>

  <?php
  $namaUser = Yii::$app->user->identity->username;
  ?>

  <div style="position: relative;">
    <span
      style="position: absolute; top: 0px; right: 0; font-size: 12px; color: #2563EB; cursor: pointer;"
      onclick="document.getElementById('assign-dropdown').value = '<?= $namaUser ?>';">
      Assign to me
    </span>
    <?= $form->field($model, 'assign')->textInput(['required' => true])->dropDownList(
      $assignList,
      [
        'prompt' => '-- Pilih Petugas --',
        'id' => 'assign-dropdown',
        'options' => [$model->assign => ['Selected' => true]],
      ]
    ) ?>
  </div>


  <div class="row">
    <div class="col-md-6">
      <div class="row">
        <div class="col-md-6">
          Due Date
        </div>
        <div class="col-md-6">
          <?= $form->field($model, 'duedate_task')->textInput([
            'type' => 'date',
            'required' => true,
            'value' => $model->duedate_task ? date('Y-m-d', strtotime($model->duedate_task)) : date('Y-m-d')
          ])->label(false)
          ?>

        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          Finishdate Task
        </div>
        <div class="col-md-6">
          <?= $form->field($model, 'finishdate_task')->textInput(['required' => true, 'type' => 'date', 'value' => $model->finishdate_task ? date('Y-m-d', strtotime($model->finishdate_task)) : date('Y-m-d')])->label(false) ?>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="row">
        <div class="col-md-6">
          Status
        </div>
        <div class="col-md-6">
          <?php
          $statusList = [
            'Open' => 'Open',
            'In Progress' => 'In Progress',
            'Done' => 'Done',
            'Merge' => 'Merge',
          ];
          if ($jumlahTask == true && $model->status != "In Progress") {
            unset($statusList['In Progress']);
          }
          ?>
          <?= $form->field($model, 'status')->dropDownList($statusList, ['options' => [$model->status => ['Selected' => true]]])->label(false) ?>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          Priority Task
        </div>
        <div class="col-md-6">
          <?= $form->field($model, 'priority_task')->dropDownList(['Low' => 'Low', 'Medium' => 'Medium', 'High' => 'High'], ['options' => [$model->priority_task => ['Selected' => true]]])->label(false) ?>
        </div>
      </div>

    </div>
  </div>


  <?= $form->field($model, 'description')->widget(CKEditor::class, [
    'options' => ['rows' => 6, 'id' => 'ckeditor-description'],
    'preset' => 'basic'
  ]) ?>


  <div class="sub-task-section">

    <div id="sub-tasks-container">
    </div>

    <div class="sub-task-header">
      <h4>Tambah Sub Task</h4>
      <?= Html::button('<i class="bi bi-plus"></i> Tambah Sub Task', ['class' => 'btn btn-secondary btn-sm add-sub-task-button', 'id' => 'add-sub-task']) ?>
    </div>
  </div>
  <?= Html::hiddenInput('returnUrl', $returnUrl) ?>
  <div class="form-group d-flex justify-content-between">
    <?= Html::button('Back', ['class' => 'btn btn-secondary btn-sm', 'onclick' => '$("#modal").modal("hide");']) ?>
    <?= Html::submitButton('<i class="fa fa-save"></i> ' . 'Simpan', ['class' => 'btn btn-primary btn-sm', "id" => "btn-save"]) ?>
  </div>

  <?php ActiveForm::end(); ?>
</div>

<?php
$this->registerJs(
  <<<JS
  $(document).ready(function() {

    if (typeof subTasks !== 'undefined' && subTasks.length > 0) {
      $('#sub-tasks-container').empty();

      subTasks.forEach((sub, index) => {
        const totalItems = sub.itemSubtasks?.length || 0;
        const checkedItems = sub.itemSubtasks?.filter(i => i.status == 1).length || 0;
        const percentage = totalItems > 0 ? Math.round((checkedItems / totalItems) * 100) : 0;

        let subTaskHtml = '<div class="sub-task-item" data-index="' + index + '">' +
          '<div class="form-group">' +
            '<label class="control-label">Title Sub Task</label>' +
            '<input type="text" class="form-control" name="sub_tasks[' + index + '][title_subtask]" value="' + sub.title_subtask + '" required>' +
          '</div>' +

          '<div class="progress mb-2">' +
            '<div class="progress-bar bg-success" role="progressbar" style="width: ' + percentage + '%;" aria-valuenow="' + percentage + '" aria-valuemin="0" aria-valuemax="100">' +
              percentage + '%' +
            '</div>' +
          '</div>';

        if (sub.itemSubtasks && sub.itemSubtasks.length > 0) {
          sub.itemSubtasks.forEach((item, i) => {
            subTaskHtml += '<div class="form-group d-flex align-items-center gap-2">' +
              '<input type="checkbox" class="form-check-input mt-0" name="sub_tasks[' + index + '][item_subtasks][' + i + '][status]" ' + (item.status == 1 ? 'checked' : '') + '>' +
              '<input type="text" class="form-control" name="sub_tasks[' + index + '][item_subtasks][' + i + '][value]" value="' + item.item_subtask + '" required>' +
              '<button type="button" class="btn btn-danger btn-sm remove-item-subtask">&times;</button>' +
            '</div>';
          });
        }

        subTaskHtml +=
          '<div class="item-container"></div>' +
          '<button type="button" class="btn btn-sm btn-secondary add-item-subtask">+ Tambah Item</button>' +
          '<button type="button" class="btn btn-sm btn-secondary remove-sub-task"><span class="remove-sub-task" style="cursor:pointer; color:red; font-weight:bold;">&times;</span> Hapus Sub Task</button>' +
          '<div class="w-full border-t-2 border-black h-2 my-2"></div>' +
        '</div>';

        $('#sub-tasks-container').append(subTaskHtml);
      });
    } else {
      $('#sub-tasks-container').empty();
    }


    $(document).on('change', '.sub-task-item input[type="checkbox"]', function () {
      const subTaskItem = $(this).closest('.sub-task-item');
      updateProgressBar(subTaskItem);
    });


    // Tambah Subtask
    let subTaskCounter = 0;
    $('#add-sub-task').off('click').on('click', function () {
      subTaskCounter++;
      let newSubTask = $('<div class="sub-task-item" data-index="' + subTaskCounter + '">' +
        '<div class="form-group">' +
          '<label class="control-label">Title Sub Task</label>' +
          '<input type="text" class="form-control" name="sub_tasks[' + subTaskCounter + '][title_subtask]" required>' +
        '</div>' +

        '<div class="progress mb-2">' +
          '<div class="progress-bar bg-success" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>' +
        '</div>' +

        '<div class="form-group d-flex align-items-center gap-2">' +
          '<input type="checkbox" class="form-check-input mt-0" name="sub_tasks[' + subTaskCounter + '][item_subtasks][0][status]">' +
          '<input type="text" class="form-control" name="sub_tasks[' + subTaskCounter + '][item_subtasks][0][value]" required>' +
          '<button type="button" class="btn btn-danger btn-sm remove-item-subtask">&times;</button>' +
        '</div>' +


        '<div class="item-container"></div>' +

        '<button type="button" class="btn btn-sm btn-secondary add-item-subtask">+ Tambah Item</button>' +
        '<button type="button" class="btn btn-sm btn-secondary remove-sub-task">' +
          '<span style="cursor:pointer; color:red; font-weight:bold;">&times;</span> Hapus Sub Task' +
        '</button>' +
        '<div class="w-full border-t-2 border-black h-2 my-2"></div>' +
      '</div>');



      $('#sub-tasks-container').append(newSubTask);
    });

    // Tambah Item Subtask
    // $(document).on('click', '.add-item-button', function() {
    //   let newItem = $('<div class="form-group">' +
    //     '<input type="text" class="form-control" name="sub_task_item[]" placeholder="Item Title">' +
    //   '</div>');
    //   $(this).siblings('.item-container').append(newItem);
    // });

    function updateProgressBar(subTaskItem) {
      const checkboxes = subTaskItem.find('input[type="checkbox"]');
      const checked = checkboxes.filter(':checked').length;
      const total = checkboxes.length;

      const percentage = total > 0 ? Math.round((checked / total) * 100) : 0;

      const progressBar = subTaskItem.find('.progress-bar');
      progressBar.css('width', percentage + '%');
      progressBar.attr('aria-valuenow', percentage);
      progressBar.text(percentage + '%');
    }

    // Hapus Subtask
    $(document).on('click', '.remove-sub-task', function() {
      $(this).closest('.sub-task-item').remove();
    });

    $(document).off('click', '.add-item-subtask').on('click', '.add-item-subtask', function () {
      const parent = $(this).closest('.sub-task-item');
      const index = parent.data('index');
      
      // Cari index item terakhir lalu +1
      const itemIndex = parent.find('.item-container .form-group').length +
                        parent.find('> .form-group.d-flex').length;

      const newItem = $(
        '<div class="form-group d-flex align-items-center gap-2">' +
          '<input type="checkbox" class="form-check-input mt-0" name="sub_tasks[' + index + '][item_subtasks][' + itemIndex + '][status]">' +
          '<input type="text" class="form-control" name="sub_tasks[' + index + '][item_subtasks][' + itemIndex + '][value]" required>' +
          '<button type="button" class="btn btn-danger btn-sm remove-item-subtask">&times;</button>' +
        '</div>'
      );

      parent.find('.item-container').append(newItem);

      updateProgressBar(parent);

    });

    $(document).on('click', '.remove-item-subtask', function () {
      const subTaskItem = $(this).closest('.sub-task-item');
      $(this).closest('.form-group').remove();

      updateProgressBar(subTaskItem);
    });


  });
JS
);
?>