<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Deals */
/* @var array $customers */ // Tambahkan ini
/* @var array $products */   // Tambahkan ini

$this->title = 'Create Deals';
$this->params['breadcrumbs'][] = ['label' => 'Deals', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="deals-create">
    <?= $this->render('_form', [
        'model' => $model,
        // 'customers' => $customers, // Pastikan ini dilewatkan
        // 'products' => $products,   // Pastikan ini dilewatkan
    ]) ?>
</div>