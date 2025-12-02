<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\grid\ActionColumn;
use yii\web\View;
use app\widgets\JSRegister;
use kartik\grid\GridView;

?>
<style>
    table {
        border: 1px solid #C0BFC0;
    }

    table th a {
        color: #3F4254 !important;
        text-decoration: none;
    }

    table th a:hover {
        color: #333 !important;
    }

    /* Hilangkan semua border dulu */
    .table-custom,
    .table-custom th,
    .table-custom td {
        border: none !important;
    }

    /* Tambahkan hanya border horizontal (bottom untuk setiap baris) */
    .table-custom tr td,
    .table-custom tr th {
        border-bottom: 1px solid #ccc !important;
        /* kamu bisa ganti warna/ketebalan */
    }

    /* Opsional: tambahkan border-top hanya untuk header */
    .table-custom thead tr th {
        border-top: 1px solid #ccc !important;
        background-color: #E5E7EB;
        /* warna abu untuk header */
        color: #3F4254;
    }

    .kv-grid-table,
    .kv-grid-table th,
    .kv-grid-table td {
        border: none !important;
    }

    .kv-grid-table tr td,
    .kv-grid-table tr th {
        border-bottom: 1px solid #ccc !important;
    }

    .kv-grid-table tbody tr:last-child td {
        border-bottom: none !important;
    }
</style>
<?php

echo GridView::widget([
    'dataProvider' => $task['dataProvider'],
    'bordered' => false,
    'striped' => false,
    'summary' => '',
    'tableOptions' => [
        'class' => 'table',
        'style' => 'border-collapse: collapse;'
    ],
    'rowOptions' => function ($model, $key, $index, $grid) {
        return ['style' => 'border: none; color:black;'];
    },
    'columns' => [
        [
            'class' => 'yii\grid\SerialColumn',
            'header' => 'No',
            'headerOptions' => [
                'style' => 'background-color: #E5E7EB; border: none; color: #3F4254; text-align: center;'
            ],
            'contentOptions' => [
                'style' => 'border: none; text-align: center; background-color: white;'
            ],
        ],
        [
            'attribute' => 'title',
            'headerOptions' => ['style' => 'background-color: #E5E7EB; border: none; color: #3F4254'],
            'contentOptions' => ['style' => 'border: none;'],
        ],

        [
            'header' => 'Due date',
            'attribute' => 'duedate_task',
            'headerOptions' => ['style' => 'background-color: #E5E7EB; border: none; color: #3F4254'],
            'contentOptions' => ['style' => 'border: none;'],
            'value' => function ($model) {
                if (!empty($model['duedate_task'])) {
                    return date('d/m/Y', strtotime($model['duedate_task']));
                }
                return "-";
            }
        ],
        [
            'header' => 'Finish date',
            'attribute' => 'finishdate_task',
            'headerOptions' => ['style' => 'background-color: #E5E7EB; border: none; color: #3F4254'],
            'contentOptions' => ['style' => 'border: none;'],
            'value' => function ($model) {
                if (!empty($model['finishdate_task'])) {
                    return date('d/m/Y', strtotime($model['finishdate_task']));
                }
                return "-";
            }
        ],
        [
            'header' => 'Priority',
            'headerOptions' => ['style' => 'background-color: #E5E7EB; border: none; color: #3F4254'],
            'contentOptions' => ['style' => 'border: none;'],
            'format' => 'raw',
            'value' => function ($model) {
                if (!empty($moda['priority_task'])) {
                    $class = match (strtolower($model['priority_task'])) {
                        'high' => 'px-4 py-1 rounded-2xl bg-red-500 text-white text-sm font-semibold',
                        'medium' => 'px-4 py-1 rounded-2xl bg-yellow-400 text-white text-sm font-semibold',
                        'low' => 'px-4 py-1 rounded-2xl bg-green-500 text-white text-sm font-semibold',
                        default => 'px-4 py-1 rounded-2xl bg-gray-300 text-white text-sm font-semibold',
                    };
                    return Html::tag('span', $model['priority_task'] ?? '', ['class' => $class]);
                }
            },
        ],
        [
            'header' => 'Status',
            'headerOptions' => ['style' => 'background-color: #E5E7EB; border: none; color: #3F4254'],
            'contentOptions' => ['style' => 'border: none;'],
            'format' => 'raw',
            'value' => function ($model) {
                $class = match (strtolower($model['status'])) {
                    'done' => 'px-4 py-1 rounded-2xl bg-green-500 text-white text-sm font-semibold',
                    'in progress' => 'px-4 py-1 rounded-2xl bg-red-500 text-white text-sm font-semibold',
                    'open' => 'px-4 py-1 rounded-2xl bg-yellow-400 text-white text-sm font-semibold',
                    default => 'px-4 py-1 rounded-2xl bg-gray-300 text-black text-sm font-semibold',
                };
                return Html::tag('span', ucfirst($model['status']), ['class' => $class]);
            },
        ],
    ],
]);
