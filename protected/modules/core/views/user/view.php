<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use mdm\admin\components\Helper;

/* @var $this yii\web\View */
/* @var $model mdm\admin\models\User */

$this->title = Yii::t('app', 'Detail User');
$this->params['pageIcon'] = 'fa fa-users';
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['pageActionButton'][] = Helper::checkRoute('index') ? Html::a('<i class="fa fa-arrow-left"></i> Kembali', ['index'], ['class' => 'btn btn-primary']) : null;

$controllerId = $this->context->uniqueId . '/';

if ($model->status == 10 && Helper::checkRoute($controllerId . 'activate')) {
	$this->params['pageActionButton'][] = Html::a(Yii::t('app', '<i class="fa fa-key"></i> Activate'), ['activate', 'id' => $model->id], [
		'class' => 'btn btn-success',
		'data' => [
			'confirm' => Yii::t('app', 'Apakah akan mengaktifkan User ini?'),
			'method' => 'post',
		],
	]);
}

$this->params['pageActionButton'][] = Helper::checkRoute('delete') ? Html::a('<i class="fa fa-trash"></i> Hapus', ['delete', 'id' => $model->id], [
  'class' => 'btn btn-icon btn-sm btn-danger',
	'data' => [
		'confirm' => 'Apakah Yakin Akan Menghapus Data ini?',
		'method' => 'post',
	],
]) : null;
?>
<div class="user-view">
    <?=
    DetailView::widget([
        'model' => $model,
		'options' => [
			'class' => 'table table-striped table-sm'
		],
        'attributes' => [
            'username',
            'email:email',
            'created_at:date',
            'status',
        ],
    ])
    ?>

</div>
