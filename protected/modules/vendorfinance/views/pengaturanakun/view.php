<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Pengaturanakun */

$this->title = $model->pengaturanakun_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Pengaturanakun'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="pengaturanakun-view">
  <?= DetailView::widget([
  'model' => $model,
  'options' => ['class' => 'table table-hover table-sm'],
  'attributes' => [
              'pengaturanakun_id',
            'pemasukan_id',
            'logo',
            'ttd',
            'created_at',
            'updated_at',
            'deleted_at',
            'created_by',
            'updated_by',
            'deleted_by',
  ],
  ]) ?>

</div>
