<?php

use yii\helpers\Html;
use app\widgets\grid\GridView;
use mdm\admin\components\RouteRule;
use mdm\admin\components\Configs;
use mdm\admin\components\Helper;

$context = $this->context;
$labels = $context->labels();
$this->title = Yii::t('app', $labels['Items']);
$this->params['breadcrumbs'][] = $this->title;
$this->params['pageIcon'] = 'fas fa-key';
$this->params['pageHeader'] = $this->title;
$this->params['pageActionButton'][] = Helper::checkRoute('create') ? Html::a('<i class="fa fa-plus"></i> Tambah', ['create'], ['class' => 'btn btn-primary']) : null;

$rules = array_keys(Configs::authManager()->getRules());
$rules = array_combine($rules, $rules);
unset($rules[RouteRule::RULE_NAME]);
?>
<div class="role-index">
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => [
          'class' => 'table table-sm table-striped'
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'name',
                'label' => Yii::t('app', 'Name'),
            ],
            [
                'attribute' => 'ruleName',
                'label' => Yii::t('app', 'Rule Name'),
                'filter' => $rules
            ],
            [
                'attribute' => 'description',
                'label' => Yii::t('app', 'Description'),
            ],
            [
				'class' => 'yii\grid\ActionColumn',
				'contentOptions' => function ($model, $key, $index, $column) {
					   return ['style' => 'text-align: center'];
				},
				'template' => '{view}',
				'buttons' => [
					'view' => function ($url, $model) {
						return Html::a('Edit',
							Yii::$app->urlManager->createUrl([Yii::$app->controller->uniqueId.'/view', 'id' => $model->name]),
							['title' => 'Detail', 'class' => 'btn btn-icon btn-sm btn-primary']
						);
					},
				],
			],
        ],
    ])
    ?>

</div>
