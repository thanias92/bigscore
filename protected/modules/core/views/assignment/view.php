<?php

use mdm\admin\AnimateAsset;
use mdm\admin\components\Helper;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\YiiAsset;

/* @var $this yii\web\View */
/* @var $model mdm\admin\models\Assignment */
/* @var $fullnameField string */

$userName = $model->{$usernameField};
if (!empty($fullnameField)) {
    $userName .= ' (' . ArrayHelper::getValue($model, $fullnameField) . ')';
}
$userName = Html::encode($userName);

$this->title = Yii::t('app', 'Assignment') . ' : ' . $userName;

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Assignments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $userName;
$this->params['pageIcon'] = 'fas fa-user-shield';
$this->params['pageHeader'] = $userName;

$this->params['pageActionButton'][] = Helper::checkRoute('index') ? Html::a('<i class="fa fa-arrow-left"></i> Kembali', ['index'], ['class' => 'btn btn-icon btn-sm btn-primary']) :null;

AnimateAsset::register($this);
YiiAsset::register($this);
$opts = Json::htmlEncode([
    'items' => $model->getItems(),
]);
$this->registerJs("var _opts = {$opts};");
$this->registerJs($this->render('_script.js'));
$animateIcon = ' <i class="fas fa-spinner glyphicon-refresh-animate"></i>';
?>
<div class="assignment-index">
    <div class="row">
        <div class="col-sm-5">
			<div class="form-group">
				<input class="form-control search" data-target="available" placeholder="<?=Yii::t('app', 'Search for available');?>">
			</div>
            <select multiple size="20" class="form-control list" data-target="available" style="height:280px">
            </select>
        </div>
        <div class="col-sm-2 text-center">
            <br><br>
            <?=Html::a('<i class="fas fa-angle-double-right"></i>' . $animateIcon, ['assign', 'id' => (string) $model->id], [
			'class' => 'btn btn-success btn-assign btn-block',
			'data-target' => 'available',
			'title' => Yii::t('app', 'Assign'),
			]);?><br><br>
            <?=Html::a('<i class="fas fa-angle-double-left"></i>' . $animateIcon, ['revoke', 'id' => (string) $model->id], [
			'class' => 'btn btn-danger btn-assign btn-block',
			'data-target' => 'assigned',
			'title' => Yii::t('app', 'Remove'),
			]);?>
        </div>
        <div class="col-sm-5">
			<div class="form-group">
				<input class="form-control search" data-target="assigned" placeholder="<?=Yii::t('app', 'Search for assigned');?>">
			</div>
            <select multiple size="20" class="form-control list" data-target="assigned" style="height:280px">
            </select>
        </div>
    </div>
</div>
