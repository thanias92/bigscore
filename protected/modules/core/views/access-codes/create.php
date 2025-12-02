<?php

use yii\helpers\Html;
use mdm\admin\components\Helper;

/* @var $this yii\web\View */
/* @var $model app\models\AccessCodes */

$this->title = 'Tambah Access Codes';
$this->params['breadcrumbs'][] = ['label' => 'Access Codes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['pageIcon'] = 'fa fa-edit';
$this->params['pageHeader'] = $this->title;
$this->params['pageActionButton'][] = Helper::checkRoute('index') ? Html::a('<i class="fa fa-arrow-left"></i> Kembali', ['index'], ['class' => 'btn btn-primary']) : null;
?>
<div class="access-codes-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
