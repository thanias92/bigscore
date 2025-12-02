<?php

use mdm\admin\AnimateAsset;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\YiiAsset;
use yii\widgets\DetailView;
use mdm\admin\components\Helper;

/* @var $this yii\web\View */
/* @var $model mdm\admin\models\AuthItem */
/* @var $context mdm\admin\components\ItemController */

$context = $this->context;
$labels = $context->labels();
$this->title = 'Detail '.$labels['Items'];
$this->params['pageIcon'] = 'fas fa-key';
$this->params['pageHeader'] = $this->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', $labels['Items']), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['pageActionButton'][] = Helper::checkRoute('index') ? Html::a('<i class="fa fa-arrow-left"></i> Kembali', ['index'], ['class' => 'btn btn-icon btn-sm btn-primary']) :null;
$this->params['pageActionButton'][] = Helper::checkRoute('update') ? Html::a('<i class="fa fa-edit"></i> Edit', ['update', 'id' => $model->name], ['class' => 'btn btn-icon btn-sm btn-success']) : null;
$this->params['pageActionButton'][] = Helper::checkRoute('delete') ? Html::a('<i class="fa fa-trash"></i> Hapus', ['delete', 'id' => $model->name], [
  'class' => 'btn btn-icon btn-sm btn-danger',
	'data' => [
		'confirm' => 'Apakah Yakin Akan Menghapus Data ini?',
		'method' => 'post',
	],
]) : null;
AnimateAsset::register($this);
YiiAsset::register($this);
$opts = Json::htmlEncode([
    'items' => $model->getItems(),
]);
$this->registerJs("var _opts = {$opts};");
$this->registerJs($this->render('_script.js'));
$animateIcon = ' <i class="fas fa-spinner glyphicon-refresh-animate"></i>';
?>
<div class="auth-item-view">
    <div class="row">
        <div class="col-sm-6">
            <?=
			DetailView::widget([
				'model' => $model,
				'options' => [
					'class' => 'table table-striped table-sm'
				],
				'attributes' => [
					'name',
					'description:ntext',
				],
				'template' => '<tr><th style="width:25%">{label}</th><td>{value}</td></tr>',
			]);
			?>
        </div>
		<div class="col-sm-6">
            <?=
			DetailView::widget([
				'model' => $model,
				'options' => [
					'class' => 'table table-striped table-sm'
				],
				'attributes' => [
					'ruleName',
					'data:ntext',
				],
				'template' => '<tr><th style="width:25%">{label}</th><td>{value}</td></tr>',
			]);
			?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-5">
			<div class="form-group">
				<input class="form-control search" data-target="available" placeholder="<?=Yii::t('app', 'Search for available');?>">
			</div>
            <select multiple size="20" class="form-control list" data-target="available" style="height:280px"></select>
        </div>
        <div class="col-sm-2 text-center">
			<br><br>
			<?=Html::a('<i class="fas fa-angle-double-right"></i>' . $animateIcon, ['assign', 'id' => $model->name], [
				'class' => 'btn btn-success btn-assign btn-block',
				'data-target' => 'available',
				'title' => Yii::t('app', 'Assign'),
			]);?><br>
			<?=Html::a('<i class="fas fa-angle-double-left"></i>' . $animateIcon, ['remove', 'id' => $model->name], [
				'class' => 'btn btn-danger btn-assign btn-block',
				'data-target' => 'assigned',
				'title' => Yii::t('app', 'Remove'),
			]);?>
		</div>
        <div class="col-sm-5">
			<div class="form-group">
				<input class="form-control search" data-target="assigned" placeholder="<?=Yii::t('app', 'Search for assigned');?>">
			</div>
            <select multiple size="20" class="form-control list" data-target="assigned" style="height:280px"></select>
        </div>
    </div>
</div>
