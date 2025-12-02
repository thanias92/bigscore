<?php

use kartik\select2\Select2;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $form kartik\form\ActiveForm */
/* @var $model app\models\Quotation */
/* @var $products array */
/* @var $disabled bool */
/* @var $prefix string ('', atau 'optional_') */
?>

<div class="product-table-wrapper">
    <table class="product-table">
        <thead>
            <tr>
                <th style="width: 35%;">Product</th>
                <th style="width: 18%;">Unit</th>
                <th style="width: 25%;">Price</th>
                <th style="width: 25%;">Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <?= $form->field($model, $prefix . 'product_id')->widget(Select2::class, [
                        'data' => ArrayHelper::map($products, 'id_produk', 'product_name'),
                        'options' => [
                            'placeholder' => 'Choose Product',
                            'id' => $prefix . 'product_id',
                            'disabled' => $disabled
                        ],
                        'pluginOptions' => ['allowClear' => true],
                    ])->label(false) ?>
                </td>
                <td>
                    <?= $form->field($model, $prefix . 'unit_product')->textInput([
                        'type' => 'number',
                        'min' => 1,
                        'step' => 1,
                        'id' => $prefix . 'unit_product',
                        'disabled' => $disabled
                    ])->label(false) ?>
                </td>
                <td>
                    <?= $form->field($model, $prefix . 'price_product')->textInput([
                        'type' => 'text',
                        'inputmode' => 'decimal',
                        'class' => 'currency-input form-control',
                        'id' => $prefix . 'price_product',
                        'disabled' => $disabled,
                        'style' => 'text-align: right;',
                    ])->label(false) ?>
                </td>
                <td>
                    <?= $form->field($model, $prefix . 'total')->textInput([
                        'readonly' => true,
                        'id' => $prefix . 'total',
                        'class' => 'currency-input',
                        'style' => 'text-align: right;',
                    ])->label(false) ?>
                </td>
            </tr>
        </tbody>
    </table>
</div>