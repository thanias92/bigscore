<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\grid\ActionColumn;
use yii\web\View;
use app\widgets\JSRegister;
use kartik\grid\GridView;
//kanza push ulang untuk hosting

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'bordered' => false,
    'striped' => false,
    'summary' => '',
    'tableOptions' => [
        'class' => 'table',
        'style' => 'border-collapse: collapse;'
    ],
    'rowOptions' => function ($model, $key, $index, $grid) {
        return ['style' => 'border: none;color:black;'];
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
            'attribute'         => 'nama_pelanggan',
            'headerOptions'     => ['style' => 'background-color: #E5E7EB; border: none; color: #3F4254'],
            'contentOptions'    => ['style' => 'border: none;'],
            'format' => 'raw',
            'value' => function ($model) {
                if(count($model['detail']) > 0) {
                    return Html::a(
                        Html::encode($model['nama_pelanggan']),
                        ['proses', 'deals_id' => $model['deals_id']],
                        [
                            'class' => 'btn btn-link p-0 text-decoration-underline',
                            'title' => '',
                        ]
                    );

                }else {
                    return Html::button(
                        Html::encode($model['nama_pelanggan']),
                        [
                            'value' => Url::to(['alert', 'deals_id' => $model['deals_id']]),
                            'title' => '',
                            'class' => 'showModalButton btn btn-link p-0 text-decoration-underline',
                        ]
                    );
                }
                
            }
        ],
        [
            'attribute' => 'email',
            'headerOptions' => ['style' => 'background-color: #E5E7EB; border: none; color: #3F4254'],
            'contentOptions' => ['style' => 'border: none;'],
        ],
        [
            'attribute' => 'no_telp',
            'headerOptions' => ['style' => 'background-color: #E5E7EB; border: none; color: #3F4254'],
            'contentOptions' => ['style' => 'border: none;'],
        ],
        [
            'attribute' => 'kontak_pribadi',
            'headerOptions' => ['style' => 'background-color: #E5E7EB; border: none; color: #3F4254'],
            'contentOptions' => ['style' => 'border: none;'],
        ],
        [
            'attribute' => 'nama_produk',
            'headerOptions' => ['style' => 'background-color: #E5E7EB; border: none; color: #3F4254'],
            'contentOptions' => ['style' => 'border: none;'],
        ],
        [
            'attribute' => 'duration',
            'headerOptions' => ['style' => 'background-color: #E5E7EB; border: none; color: #3F4254'],
            'contentOptions' => ['style' => 'border: none;'],
        ],
        [
            'attribute' => 'status',
            'format' => 'raw',
            'headerOptions' => ['style' => 'background-color: #E5E7EB; border: none; color: #3F4254'],
            'contentOptions' => ['style' => 'border: none;'],
            'value' => function ($model) {
                $status = $model['status'] ?? 'Open';
                if ($status === 'Done') {
                    return '<span style="background-color:#D1FAE5;color:#065F46;padding:4px 8px;border-radius:6px;">Done</span>';
                } elseif ($status === 'Open') {
                    return '<span style="background-color:#FEE2E2;color:#991B1B;padding:4px 8px;border-radius:6px;">Open</span>';
                } elseif ($status === 'In Progress') {
                    return '<span style="background-color:#DBEAFE;color:#1E40AF;padding:4px 8px;border-radius:6px;">In Progress</span>';
                } else {
                    return '<span style="background-color:#FEE2E2;color:#1E40AF;padding:4px 8px;border-radius:6px;">Open</span>';
                }
            }
        ],


    ],
]);
