<?php

use yii\helpers\Html;
use mdm\admin\components\Helper;

/* @var $this yii\web\View */
/* @var $model mdm\admin\models\AuthItem */
/* @var $context mdm\admin\components\ItemController */

$context = $this->context;
$labels = $context->labels();
$this->title = Yii::t('app', 'Update ' . $labels['Item']);
$this->params['pageIcon'] = 'fas fa-key';
$this->params['pageHeader'] = $this->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', $labels['Items']), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->name]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');

$this->params['pageActionButton'][] = Helper::checkRoute('index') ? Html::a('<i class="fa fa-arrow-left"></i> Kembali', ['index'], ['class' => 'btn btn-primary']) :null;
?>
<div class="auth-item-update">
    <?=
    $this->render('_form', [
        'model' => $model,
    ]);
    ?>
</div>
