<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Vendor;
/* @var $this yii\web\View */
/* @var $model app\models\Pengeluaran */

$this->title = $model->id_pengeluaran;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Pengeluaran'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="pengeluaran-view">
  <?= DetailView::widget([
  'model' => $model,
  'options' => ['class' => 'table table-hover table-sm'],
  'attributes' => [
              'id_pengeluaran',
            'tanggal',
            'jumlah',
            'jenis_pembayaran',
            [
              'attribute' => 'id_vendor',
              'label' => 'Nama Vendor',
              'value' => function ($model) {
                  return $model->vendor->nama_vendor ?? '(Tidak ada nama)';
              }
          ],
            'keterangan',
            'created_by',
            'updated_by',
            'deleted_by',
            'created_at',
            'updated_at',
            'deleted_at',
  ],
  ]) ?>

</div>
