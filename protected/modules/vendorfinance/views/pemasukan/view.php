<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Pemasukan */

$this->title = $model->pemasukan_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Pemasukan'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="pemasukan-view">
  <?= DetailView::widget([
    'model' => $model,
    'options' => ['class' => 'table table-hover table-sm'],
    'attributes' => [
      [
        'label' => 'Jenis Pembelian',
        'value' => $model->deals->purchase_type ?? '(Tidak tersedia)',
      ],
      'purchase_date',
      [
        'label' => 'Price Product',
        'value' => $model->deals->product->harga ?? '(Tidak tersedia)',
      ],
      'total',
      'description:ntext',
    ],
  ]) ?>

</div>