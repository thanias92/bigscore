<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Implementation */
//kanza push ulang untuk hosting

$this->title = $model->id_implementasi;
$this->params['breadcrumbs'][] = ['label' => 'Implementation', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="implementation-view">
  <?= DetailView::widget([
    'model' => $model,
    'options' => ['class' => 'table table-hover table-sm'],
    'attributes' => [
      'id_implementasi',
      'activity_title',
      'activity',
      'detail',
      'start_date',
      'completion_date',
      'pic_aktivitas',
      'status',
      'notes:ntext',
      'duration',
      'line_progress',
      'created_by',
      'updated_by',
      'deleted_by',
      'created_at',
      'updated_at',
      'deleted_at',
    ],
  ]) ?>

</div>