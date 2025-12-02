<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Product */

$this->title = $model->id_produk;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Product'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="product-view">
  <?= DetailView::widget([
    'model' => $model,
    'options' => ['class' => 'table table-hover table-sm'],
    'attributes' => [
      'id_produk',
      // 'no_produk',
      'code_produk',
      'keterangan',
      'unit',
      'product_name',
      [
        'attribute' => 'harga',
        'value' => function ($model) {
          return 'Rp' . number_format((int)$model->harga, 0, ',', '.');
        },
      ],
      // 'created_by',
      // 'updated_by',
      // 'deleted_by',
      // 'created_at',
      // 'updated_at',
      // 'deleted_at',
    ],
  ]) ?>

</div>