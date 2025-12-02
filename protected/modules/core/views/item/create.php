<?php

use yii\helpers\Html;
use mdm\admin\components\Helper;

$context = $this->context;
$labels = $context->labels();
$this->title = Yii::t('app', 'Tambah ' . $labels['Item']);
$this->params['pageIcon'] = 'fas fa-key';
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', $labels['Items']), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['pageHeader'] = $this->title;
$this->params['pageActionButton'][] = Helper::checkRoute('index') ? Html::a('<i class="fa fa-arrow-left"></i> Kembali', ['index'], ['class' => 'btn btn-primary']) : null;
?>
<div class="auth-item-create">
    <?=
    $this->render('_form', [
        'model' => $model,
    ]);
    ?>
</div>
