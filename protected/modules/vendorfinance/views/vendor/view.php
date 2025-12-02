<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Vendor */

$this->title = $model->id_vendor;
$this->params['breadcrumbs'][] = ['label' => 'Vendor', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="vendor-view">
  <?= DetailView::widget([
  'model' => $model,
  'options' => ['class' => 'table table-hover table-sm'],
  'attributes' => [
            // 'id_vendor',
            'nama_vendor',
            'alamat_vendor',
            'email_vendor',
            'telp_vendor',
            'nama_PIC',
            'email_PIC:email',
            'telp_PIC',
            // 'created_by',
            // 'updated_by',
            // 'deleted_by',
            // 'created_at',
            // 'updated_at',
            // 'deleted_at',
  ],
  ]) ?>

</div>
