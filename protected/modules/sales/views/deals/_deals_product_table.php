<?php

use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form kartik\form\ActiveForm */
/* @var $model app\models\Deals */
/* @var $products array */
/* @var $disabled bool */
?>

<div class="product-table-wrapper">
    <table class="product-table">
        <thead>
            <tr>
                <th style="width: 32%;">Product</th>
                <th style="width: 14%;">Unit</th>
                <th style="width: 22%;">Price</th>
                <th style="width: 25%;">Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <?= $form->field($model, 'product_id')->widget(Select2::class, [
                        'data' => ArrayHelper::map($products, 'id_produk', 'product_name'),
                        'initValueText' => $model->product->product_name ?? '',
                        'options' => [
                            'placeholder' => 'Choose Product',
                            'id' => 'deals-product_id',
                            'disabled' => $disabled
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'dropdownParent' => new \yii\web\JsExpression("$('#modal')")
                        ],
                    ])->label(false) ?>
                </td>
                <td>
                    <?= $form->field($model, 'unit_product')->textInput([
                        'type' => 'number',
                        'min' => 1,
                        'step' => 1,
                        'id' => 'deals-unit_product',
                        'disabled' => $disabled
                    ])->label(false) ?>
                </td>
                <td>
                    <?= $form->field($model, 'price_product')->textInput([
                        'type' => 'text',
                        'inputmode' => 'decimal',
                        'class' => 'currency-input form-control',
                        'id' => 'deals-price_product',
                        'disabled' => $disabled,
                        'style' => 'text-align: right;',
                    ])->label(false) ?>
                </td>
                <td>
                    <?= $form->field($model, 'total')->textInput([
                        'readonly' => true,
                        'id' => 'deals-line-total',
                        'class' => 'currency-input form-control',
                        'style' => 'text-align: right;',
                    ])->label(false) ?>
                </td>
            </tr>
        </tbody>
    </table>
</div>