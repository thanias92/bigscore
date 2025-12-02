<?php

use yii\helpers\Html;
use yii\widgets\DetailView; //(test)

/* @var $this yii\web\View */
/* @var $model app\models\Deals */

$this->title = $model->deals_id;
$this->params['breadcrumbs'][] = ['label' => 'Deals', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="deals-view">
  <?= DetailView::widget([
    'model' => $model,
    'options' => ['class' => 'table table-hover table-sm'],
    'attributes' => [
      'deals_id',
      [
        'attribute' => 'customer_id',
        'value' => function ($model) {
          return $model->customer->customer_name ?? 'N/A'; // Asumsi model customer punya attribute customer_name
        },
        'label' => 'Customer Name',
      ],
      [
        'attribute' => 'product_id',
        'value' => function ($model) {
          return $model->product->product_name ?? 'N/A'; // Asumsi model product punya attribute product_name
        },
        'label' => 'Product Name',
      ],
      'total',
      'price_product',
      'label_deals',
      'purchase_type',
      'purchase_date:date', // Tampilkan sebagai tanggal
      'description:ntext', // Tampilkan sebagai teks biasa, dengan newline
      'created_by', // Akan menampilkan ID user untuk sementara
      'updated_by', // Akan menampilkan ID user untuk sementara
      'deleted_by', // Akan menampilkan ID user untuk sementara
      'created_at',
      'updated_at',
      'deleted_at',
    ],
  ]) ?>

</div>