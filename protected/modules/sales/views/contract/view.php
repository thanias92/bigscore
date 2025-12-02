<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Contract */

$this->title = $model->contract_id;
$this->params['breadcrumbs'][] = ['label' => 'Contract', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="contract-view">
  <?= DetailView::widget([
    'model' => $model,
    'options' => ['class' => 'table table-hover table-sm'],
    'attributes' => [
      'contract_id',
      'contract_code',
      'invoice_id',
      'start_date',
      'end_date',
      [
        'attribute' => 'evidence_contract',
        'format' => 'raw',
        'value' => function ($model) {
          return $model->evidence_contract
            ? Html::a('Lihat File', Yii::getAlias('@web') . '/' . $model->evidence_contract, ['target' => '_blank'])
            : '(Tidak ada file)';
        }
      ],
      'status_contract',
      'description:ntext',
      'created_by',
      'updated_by',
      'deleted_by',
      'created_at',
      'updated_at',
      'deleted_at',
    ],
  ]) ?>

</div>