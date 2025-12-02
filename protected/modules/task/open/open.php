<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\web\View;
use app\widgets\JSRegister;
use yii\bootstrap5\Modal;

/* @var $this yii\web\View */
/* @var $tasks array of Task models */

$this->title = 'Task Board';
$this->params['breadcrumbs'][] = $this->title;

// Define status categories. Make sure these match the possible values of your 'status' field.
$taskStatuses = [
    'Open' => 'Open',
    'In Progress' => 'In Progress',
    'Done' => 'Done',
    'Merge' => 'Merge',
];

?>

<?php Modal::begin([
    'headerOptions' => ['id' => 'modalHeader'],
    'id' => 'modal',
    'size' => 'modal-xl',
    'options' => [
        'data-bs-backdrop' => 'static',
        'data-bs-keyboard' => 'false',
        'tabindex' => false,
    ],
]);
echo "<div id='modalContent'></div>";
Modal::end();
?>

<div class="card">
    <div class="card-header d-flex">
        <div class="header-title flex-grow-1">
            <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
        </div>
        <div class="ms-3">
            <?= Html::button('Tambah Task', ['value' => Url::to(['create']), 'title' => 'Form Task', 'class' => 'showModalButton btn btn-primary']); ?>
        </div>
    </div>
    <div class="card-body">
        <?php Pjax::begin(['id' => 'task-board', 'timeout' => false, 'enablePushState' => false, 'enableReplaceState' => false]); ?>
        <div class="row">
            <?php foreach ($taskStatuses as $statusKey => $statusName) : ?>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title"><?= Html::encode($statusName) ?></h5>
                        </div>
                        <div class="card-body task-column" id="task-column-<?= strtolower(str_replace(' ', '-', $statusKey)) ?>">
                            <?php
                            foreach ($tasks as $task) :
                                if ($task->status === $statusKey) :
                            ?>
                                    <div class="card mb-3 task-card" data-task-id="<?= $task->id_task ?>">
                                        <div class="card-body">
                                            <h6 class="card-title"><?= Html::encode($task->title) ?></h6>
                                            <p class="card-text">
                                                Label: <?= Html::encode($task->label_task) ?><br>
                                                Modul: <?= Html::encode($task->modul) ?><br>
                                                Due: <?= Html::encode($task->duedate_task) ?>
                                            </p>
                                            <div class="d-flex justify-content-end">
                                                <?= Html::a('View', ['/task/view', 'id_task' => $task->id_task], ['class' => 'btn btn-primary btn-sm me-1']) ?>
                                                <?= Html::a('Edit', ['/task/update', 'id_task' => $task->id_task], ['class' => 'btn btn-success btn-sm me-1 showModalButton']) ?>
                                                <?= Html::a('Delete', ['/task/delete', 'id_task' => $task->id_task], [
                                                    'class' => 'btn btn-danger btn-sm',
                                                    'data' => [
                                                        'confirm' => 'Are you sure you want to delete this item?',
                                                        'method' => 'post',
                                                    ],
                                                ]) ?>
                                            </div>
                                        </div>
                                    </div>
                            <?php
                                endif;
                            endforeach;
                            if (array_filter($tasks, function ($task) use ($statusKey) {
                                return $task->status === $statusKey;
                            }) === []) {
                                echo '<p class="text-muted">No tasks in this status.</p>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php Pjax::end(); ?>
    </div>
</div>

<style>
    .task-column {
        min-height: 200px;
        /* Adjust as needed */
        border: 1px solid #eee;
        padding: 10px;
    }

    .task-card {
        border-left: 5px solid #007bff;
        /* Default color */
    }

    /* You can add more specific styles based on task properties if needed */
</style>

<?php
// You might need to adjust the JSRegister part based on your existing code
JSRegister::begin(['position' => View::POS_END]);
?>
<script>
    // Your existing JavaScript for modal and delete functionality can remain here
    $(document).on("click", ".showModalButton", function() {
        var value = $(this).attr("value");
        var title = $(this).attr("title");
        // ... your modal display logic ...
    });

    $(document).on("click", ".delete", function(e) {
        e.preventDefault();
        var deleteUrl = $(this).attr('href');
        // ... your delete confirmation logic ...
    });
</script>
<?php JSRegister::end(); ?>