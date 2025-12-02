<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Laporan */

$this->title = $model->laporan_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Laporan'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="laporan-view">
  <?= DetailView::widget([
  'model' => $model,
  'options' => ['class' => 'table table-hover table-sm'],
  'attributes' => [
              'laporan_id',
            'pemasukan_id',
            'pengeluaran_id',
            'tanggal',
            'tipe_laporan',
            'jumlah_pemasukan',
            'jumlah_pengeluaran',
            'saldo_akhir',
            'created_by',
            'updated_by',
            'deleted_by',
            'created_at',
            'updated_at',
            'deleted_at',
  ],
  ]) ?>

</div>
