<?php

use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $form kartik\form\ActiveForm */
/* @var $model app\models\Contract */

// Ambil data dari relasi.
$productName = $model->product->product_name ?? '';
$unit = $model->deals->unit_product ?? '';
$price = $model->deals->price_product ?? 0;
$total = $model->deals->total ?? 0;

// Format harga dan total ke dalam format Rupiah
$priceFormatted = Yii::$app->formatter->asCurrency($price, 'IDR');
$totalFormatted = Yii::$app->formatter->asCurrency($total, 'IDR');
?>

<div class="product-table-wrapper">
    <table class="product-table">
        <thead>
            <tr>
                <th style="width: 40%;">Product</th>
                <th style="width: 15%;">Unit</th>
                <th style="width: 25%;">Price</th>
                <th style="width: 25%;">Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <?= $form->field($model, 'product_id', ['options' => ['class' => 'mb-0']])
                        ->textInput(['value' => $productName, 'readonly' => true, 'id' => 'contract-product-name'])
                        ->label(false) ?>
                </td>
                <td>
                    <?= $form->field($model, 'unit_product', ['options' => ['class' => 'mb-0']])
                        ->textInput(['value' => $unit, 'readonly' => true, 'id' => 'contract-unit'])
                        ->label(false) ?>
                </td>
                <td>
                    <?= $form->field($model, 'price_product', ['options' => ['class' => 'mb-0']])
                        ->textInput(['value' => $priceFormatted, 'readonly' => true, 'id' => 'contract-price', 'style' => 'text-align: right;'])
                        ->label(false) ?>
                </td>
                <td>
                    <?= $form->field($model, 'total', ['options' => ['class' => 'mb-0']])
                        ->textInput(['value' => $totalFormatted, 'readonly' => true, 'id' => 'contract-total', 'style' => 'text-align: right;'])
                        ->label(false) ?>
                </td>
            </tr>
        </tbody>
    </table>
</div>