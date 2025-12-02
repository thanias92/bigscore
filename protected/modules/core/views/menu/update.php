<?php

use yii\helpers\Html;
use mdm\admin\components\Helper;

/* @var $this yii\web\View */
/* @var $model app\models\Menu */

$this->title = 'Edit Menu';
$this->params['breadcrumbs'][] = ['label' => 'Menu', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Edit Menu';

$this->params['pageIcon'] = 'fa fa-edit';
$this->params['pageHeader'] = $this->title;
$this->params['pageActionButton'][] = Helper::checkRoute('index') ? Html::a('<i class="fa fa-arrow-left"></i> Kembali', ['index'], ['class' => 'btn btn-primary']) :null;
?>
<div class="menu-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
