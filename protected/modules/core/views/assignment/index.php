<?php

use mdm\admin\components\Helper;
use yii\helpers\Html;
use app\widgets\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel mdm\admin\models\searchs\Assignment */
/* @var $usernameField string */
/* @var $extraColumns string[] */

$this->title = Yii::t('app', 'Assignments');
$this->params['breadcrumbs'][] = $this->title;
$this->params['pageIcon'] = 'fa fa-user-shield';
$this->params['pageHeader'] = $this->title;
$columns = [
    ['class' => 'yii\grid\SerialColumn'],
    $usernameField,
];
if (!empty($extraColumns)) {
    $columns = array_merge($columns, $extraColumns);
}
$columns[] = [
	'class' => 'yii\grid\ActionColumn',
	'contentOptions' => function ($model, $key, $index, $column) {
		   return ['style' => 'text-align: center'];
	},
	'template' => '{view}',
	'buttons' => [
		'view' => function ($url, $model) {
			return Html::a('Edit',
				Yii::$app->urlManager->createUrl([Yii::$app->controller->uniqueId.'/view', 'id' => $model->id]),
				['title' => 'Detail', 'class' => 'btn btn-icon btn-sm btn-primary']
			);
		},
	]
];
?>
<div class="assignment-index">
    <?php Pjax::begin(); ?>
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
		'tableOptions' => [
			'class' => 'table table-sm table-striped'
		],
        'columns' => $columns,
    ]);
    ?>
    <?php Pjax::end(); ?>

</div>
