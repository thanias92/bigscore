<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\AccountKeluar */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Account Keluar'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="account-keluar-view">
  <?= DetailView::widget([
  'model' => $model,
  'options' => ['class' => 'table table-hover table-sm'],
  'attributes' => [
              'id',
            'code',
            'akun',
            'created_by',
            'updated_by',
            'deleted_by',
            'created_at',
            'updated_at',
            'deleted_at',
  ],
  ]) ?>

</div>
