<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Pengaturanakun */

$this->title = 'Ubah Pengaturan Akun #' . $model->pengaturanakun_id;
?>
<div class="pengaturanakun-update">
  <?= $this->render('_form', ['model'=>$model]) ?>
</div>
