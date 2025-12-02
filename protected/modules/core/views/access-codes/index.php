<?php

use yii\helpers\Html;
use yii\helpers\Url;
use mdm\admin\components\Helper;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\AccessCodesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Access Codes';
$this->params['breadcrumbs'][] = $this->title;
$this->params['pageIcon'] = 'fa fa-th-large';
$this->params['pageHeader'] = $this->title;
$this->params['pageActionButton'][] = Html::a('Cari |
                                <svg class="icon-16" width="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="11.7669" cy="11.7666" r="8.98856" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></circle>
                                    <path d="M18.0186 18.4851L21.5426 22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>', '#access-codes-search', ['class' => 'btn btn-warning', 'data-toggle' => 'collapse' , 'aria-controls' => 'access-codes-search']);
$this->params['pageActionButton'][] = Helper::checkRoute('create') ? Html::a('<i class="fa fa-plus"></i> Tambah', ['create'], ['class' => 'btn btn-primary']) : null;
?>
<div class="access-codes-index">

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'krajeeDialogSettings' => [ 'overrideYiiConfirm' => false ],
        'layout' => '<div class="row mb-1"><div class="col-sm-6">{summary}</div><div class="col-sm-6 text-right">{toolbar}</div></div>{items}{pager}',
        'exportConfig' => [
            GridView::EXCEL => ['label' => 'Save as EXCEL','filename' => 'List '.'Access Codes'.' '.date('Y-m-d-H:i:s'),],
            GridView::CSV => ['label' => 'Save as CSV', 'filename' => 'List '.'Access Codes'.' '.date('Y-m-d-H:i:s'),],
            GridView::JSON => ['label' => 'Save as JSON', 'filename' => 'List '.'Access Codes'.' '.date('Y-m-d-H:i:s'),],
        ],
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            'username',
            'password',
            'purpose',
            'meta',
            //'is_active:boolean',
            [
							'class' => 'kartik\grid\ActionColumn',
              'header' => '#',
              'dropdown' => true,
              'dropdownButton' => [
                'class' => 'btn btn-sm btn-primary btn-icon',
                'label' => '<i class="fas fa-chevron-circle-down"></i>',
                'title' => 'Klik Untuk Fungsi'
              ],
							'template' => Helper::filterActionColumn('{view} {update} {delete}'),
							'buttons' => [
								'view' => function ($url, $model) {
									return Html::a('<i class="fa fa-search"></i> Detail',
										Yii::$app->urlManager->createUrl([Yii::$app->controller->uniqueId.'/view', 'id' => $model->id_access]),
										['title' => 'Detail', 'class' => 'btn btn-icon btn-sm btn-success']
									);
								},
								'update' => function ($url, $model) {
									return Html::a('<i class="fa fa-edit"></i> Edit',
										Yii::$app->urlManager->createUrl([Yii::$app->controller->uniqueId.'/update', 'id' => $model->id_access]),
										['title' => 'Edit', 'class' => 'btn btn-icon btn-sm btn-info']
									);
								},
								'delete' => function ($url, $model) {
									  $url = Url::to([Yii::$app->controller->uniqueId.'/delete', 'id' => $model->id_access]);
									  return Html::a('<i class="fa fa-trash"></i> Hapus', $url, [
										  'title'        => 'Hapus',
										  'data-confirm' => 'Apakah Yakin Akan Menghapus Data ini?',
										  'data-method'  => 'post',
										  'class' => 'btn btn-icon btn-sm btn-danger'
									  ]);
								  }
							],
            ],
        ],
    ]); ?>
</div>
