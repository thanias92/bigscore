<?php

use mdm\admin\AnimateAsset;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\YiiAsset;

/* @var $this yii\web\View */
/* @var $routes [] */

$this->title = 'Routes';
$this->params['breadcrumbs'][] = $this->title;
$this->params['pageIcon'] = 'fas fa-link';
$this->params['pageHeader'] = $this->title;
$this->params['menuid'] = 110561;

AnimateAsset::register($this);
YiiAsset::register($this);
$opts = Json::htmlEncode([
    'routes' => $routes,
]);
$this->registerJs("var _opts = {$opts};");
$this->registerJs($this->render('_script.js'));
$animateIcon = ' <i class="fas fa-spinner glyphicon-refresh-animate"></i>';
?>
<div class="row">
    <div class="col-sm-12">
		<div class="form-group">
			<div class="input-group">
				<input id="inp-route" type="text" class="form-control" placeholder="Route">
			    <div class="input-group-append">
				  <?=Html::a('<i class="fas fa-plus"></i>' . $animateIcon, ['create'], [
						'class' => 'btn btn-primary',
						'id' => 'btn-new',
					]);?>
				</div>
			</div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-5">
		<div class="form-group">
			<div class="input-group">
				<input class="form-control search" data-target="available" placeholder="Search for available">
				<div class="input-group-append">
					<?=Html::a('<i class="fas fa-redo"></i>', ['refresh'], [
						'class' => 'btn btn-primary',
						'id' => 'btn-refresh',
					]);?>
				</div>
			</div>
		</div>
        <select multiple class="form-control list" data-target="available" style="height:280px">
		</select>
    </div>
    <div class="col-sm-2 text-center">
        <br><br>
        <?=Html::a('<i class="fas fa-angle-double-right"></i>' . $animateIcon, ['assign'], [
			'class' => 'btn btn-success btn-assign btn-block',
			'data-target' => 'available',
			'title' => 'Assign',
		]);?><br>
        <?=Html::a('<i class="fas fa-angle-double-left"></i>' . $animateIcon, ['remove'], [
			'class' => 'btn btn-danger btn-assign btn-block',
			'data-target' => 'assigned',
			'title' => 'Remove',
		]);?>
    </div>
    <div class="col-sm-5">
		<div class="form-group">
			<input class="form-control search" data-target="assigned" placeholder="Search for assigned">
		</div>
		<select multiple class="form-control list" data-target="assigned" style="height:280px">
		</select>
    </div>
</div>
