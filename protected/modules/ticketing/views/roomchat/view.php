<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Roomchat */

$this->title = $model->id_chat;
$this->params['breadcrumbs'][] = ['label' => 'Roomchat', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="roomchat-view">
  <?= DetailView::widget([
  'model' => $model,
  'options' => ['class' => 'table table-hover table-sm'],
  'attributes' => [
              'id_chat',
            'send',
            'chat',
            'send_at',
            'is_read:boolean',
            'created_by',
            'updated_by',
            'deleted_by',
            'created_at',
            'updated_at',
            'deleted_at',
  ],
  ]) ?>

</div>
