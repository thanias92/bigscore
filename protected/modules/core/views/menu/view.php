<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use mdm\admin\components\Helper;

/* @var $this yii\web\View */
/* @var $model app\models\Menu */

$this->title = 'Detail Menu';
$this->params['breadcrumbs'][] = ['label' => 'Menu', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Detail Menu';
\yii\web\YiiAsset::register($this);

$this->params['pageIcon'] = 'fas fa-search';
$this->params['pageHeader'] = $this->title;

$this->params['pageActionButton'][] = Helper::checkRoute('index') ? Html::a('<i class="fa fa-arrow-left"></i> Kembali', ['index'], ['class' => 'm-portlet__nav-link btn btn-sm btn-brand m-btn m-btn--pill m-btn--air']) :null;
$this->params['pageActionButton'][] = Helper::checkRoute('update') ? Html::a('<i class="fa fa-edit"></i> Edit', ['update', 'id' => $model->id], ['class' => 'm-portlet__nav-link btn btn-sm btn-info m-btn m-btn--pill m-btn--air']) : null;
$this->params['pageActionButton'][] = Helper::checkRoute('delete') ? Html::a('<i class="fa fa-trash"></i> Hapus', ['delete', 'id' => $model->id], [
  'class' => 'm-portlet__nav-link btn btn-sm btn-danger m-btn m-btn--pill m-btn--air',
	'data' => [
		'confirm' => 'Apakah Yakin Akan Menghapus Data ini?',
		'method' => 'post',
	],
]) : null;
?>
<div class="menu-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'parent',
            'route',
            'order',
            'data:ntext',
        ],
    ]) ?>

</div>
