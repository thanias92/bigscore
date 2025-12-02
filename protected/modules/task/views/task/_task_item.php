<?php
// protected/modules/task/views/task/_task_item.php

use yii\helpers\Html;
use app\models\Task; // Pastikan model Task di-use
use yii\helpers\Url;

/**
 * @var yii\web\View $this
 * @var Task $model
 * @var int $key
 * @var int $index
 * @var yii\widgets\ListView $widget
 */

$this->registerCssFile('https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css');
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- style="width: 320px !important; height: 180px !important; border: 1px solid #ccc; padding: 16px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); border-radius: 8px; background-color: #fff; margin: 10px; box-sizing: border-box;" -->
<!--  -->
<!--aku ambill css dari sini
    <!-- <div class="task-item p-4 rounded-sm shadow-lg bg-white h-full" style="height: 180px !important;"> -->
<div class="task-item p-4 rounded-sm bg-white h-full" style="height: 180px !important;">
    <div class="text-right text-xs text-gray-500 mb-2">
        <?= Yii::$app->formatter->asDate($model->duedate_task, 'php:d-m-Y'); ?>
    </div>

    <h3 class="text-lg font-semibold">
        <a type="button" class="showModalButton block" value="<?= Url::to(['/task/task/view', 'id_task' => $model->id_task]) ?>" title="Form Task">
            <?= Html::encode($model->title) ?>
        </a>
    </h3>
    <div class="text-sm text-gray-600 mb-2"><?= Html::encode($model->modul) ?></div>
    <div class="flex items-center text-sm mb-2">
        <img src="https://cdn-icons-png.flaticon.com/512/6522/6522516.png" alt="Assigned Task" class="w-4 h-4 mr-2">
        <span><?= Html::encode($model->assign) ?></span>
    </div>
    <?php
    $priority = '';
    $bgClass = '';
    $textClass = '';

    if (!empty($model->priority_task)) {
        $priority = strtolower($model->priority_task);

        if ($priority === 'low') {
            $bgClass = 'bg-green-100';
            $textClass = 'text-green-800';
        } elseif ($priority === 'medium') {
            $bgClass = 'bg-yellow-100';
            $textClass = 'text-yellow-800';
        } elseif ($priority === 'height' || $priority === 'high') {
            $bgClass = 'bg-red-100';
            $textClass = 'text-red-800';
        } else {
            $bgClass = 'bg-gray-100';
            $textClass = 'text-gray-800';
        }
    } else {
        // Jika kosong/null, pakai default class
        $bgClass = 'bg-gray-100';
        $textClass = 'text-gray-800';
    }
    ?>


    <div class="w-full flex flex-wrap justify-between gap-1 text-xs">
        <div class="flex flex-row gap-2">
            <div class="px-2 py-1 rounded <?= $bgClass ?> <?= $textClass ?>">
                <?= Html::encode($model->priority_task) ?>
            </div>

            <?php if (is_array($model->label_task)): ?>
                <?php foreach ($model->label_task as $label): ?>
                    <div class="px-2 py-1 rounded bg-blue-200 text-green-800"><?= Html::encode($label) ?></div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="delete-task" data-id="<?= $model->id_task ?>" data-return="<?= Yii::$app->request->url ?>">
            <i class="fas fa-trash text-blue-500 cursor-pointer text-xl"></i>
        </div>

    </div>
</div>
<!-- </a> -->