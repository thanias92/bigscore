<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\widgets\grid\GridView;
use mdm\admin\components\Helper;

/* @var $this yii\web\View */
/* @var $searchModel mdm\admin\models\searchs\User */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Users');
$this->params['breadcrumbs'][] = $this->title;
$this->params['pageIcon'] = 'fa fa-users';
$this->params['pageHeader'] = $this->title;
?>
<div class="user-index">
  <?=
  GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'tableOptions' => [
      'class' => 'table table-sm table-striped'
    ],
    'columns' => [
      ['class' => 'yii\grid\SerialColumn'],
      'username',
      'email:email',
      [
        'attribute' => 'status',
        'value' => function ($model) {
          return $model->status == 0 ? 'Inactive' : 'Active';
        },
        'filter' => [
          0 => 'Inactive',
          10 => 'Active'
        ]
      ],
      [
        'class' => 'yii\grid\ActionColumn',
        'contentOptions' => function ($model, $key, $index, $column) {
          return ['style' => 'text-align: center'];
        },
        'template' => Helper::filterActionColumn(['view', 'activate', 'delete']),
        'buttons' => [
          'activate' => function ($url, $model) {
            if ($model->status == 10) {
              return '';
            }
            $options = [
              'title' => Yii::t('app', 'Activate'),
              'aria-label' => Yii::t('app', 'Activate'),
              'data-confirm' => Yii::t('app', 'Are you sure you want to activate this user?'),
              'data-method' => 'post',
              'data-pjax' => '0',
            ];
            return Html::a('<i class="fa fa-check"></i>', $url, $options);
          },
          'view' => function ($url, $model) {
            return Html::a(
              'View',
              Yii::$app->urlManager->createUrl([Yii::$app->controller->uniqueId . '/view', 'id' => $model->id]),
              ['title' => 'Detail', 'class' => 'btn btn-icon btn-sm btn-primary']
            );
          },
          'delete' => function ($url, $model) {
            $url = Url::to([Yii::$app->controller->uniqueId . '/delete', 'id' => $model->id]);
            return Html::a('Delete', $url, [
              'title'        => 'Hapus',
              'data-confirm' => 'Apakah Yakin Akan Menghapus Data ini?',
              'data-method'  => 'post',
              'class' => 'btn btn-icon btn-sm btn-danger'
            ]);
          }
        ]
      ],
    ],
  ]);
  ?>
</div>
