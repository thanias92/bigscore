<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use mdm\admin\components\Helper;

/* @var $this yii\web\View */
/* @var $model app\models\AccessCodes */

$this->title = 'Detail Access Codes';
$this->params['breadcrumbs'][] = ['label' => 'Access Codes', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Detail Access Codes';
\yii\web\YiiAsset::register($this);

$this->params['pageIcon'] = 'fa fa-search';
$this->params['pageHeader'] = $this->title;

$this->params['pageActionButton'][] = Helper::checkRoute('index') ? Html::a('<i class="fa fa-arrow-left"></i> Kembali', ['index'], ['class' => 'btn btn-primary']) :null;
$this->params['pageActionButton'][] = Helper::checkRoute('update') ? Html::a('<i class="fa fa-edit"></i> Edit', ['update', 'id' => $model->id_access], ['class' => 'btn btn-sm btn-info']) : null;
$this->params['pageActionButton'][] = Helper::checkRoute('delete') ? Html::a('<i class="fa fa-trash"></i> Hapus', ['delete', 'id' => $model->id_access], [
  'class' => 'btn btn-sm btn-danger',
	'data' => [
		'confirm' => 'Apakah Yakin Akan Menghapus Data ini?',
		'method' => 'post',
	],
]) : null;
?>
<div class="access-codes-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id_access',
            'username',
            'password',
            'purpose',
            'meta',
            'is_active:boolean',
        ],
    ]) ?>

</div>
