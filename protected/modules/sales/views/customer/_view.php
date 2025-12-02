<?php

use yii\helpers\Html;

/* @var $model app\models\Customer */
?>

<div class="customer-view">

    <h4>Customer Information</h4>
    <table class="table table-bordered">
        <tr>
            <th>Name</th>
            <td><?= Html::encode($model->customer_name) ?></td>
        </tr>
        <tr>
            <th>Address</th>
            <td><?= nl2br(Html::encode($model->customer_address)) ?></td>
        </tr>
        <tr>
            <th>Email</th>
            <td><?= Html::encode($model->customer_email) ?></td>
        </tr>
        <tr>
            <th>Phone</th>
            <td><?= Html::encode($model->customer_phone) ?></td>
        </tr>
        <tr>
            <th>Website</th>
            <td><?= Html::encode($model->customer_website) ?></td>
        </tr>
        <tr>
            <th>Establishment Date</th>
            <td><?= Yii::$app->formatter->asDate($model->establishment_date) ?></td>
        </tr>
        <tr>
            <th>Customer Source</th>
            <td>
                <?php
                $sources = [
                    'satusehat' => 'SATUSEHAT Data',
                    'bps' => 'Badan Pusat Statistik (BPS)',
                    'eklinik' => 'eKlinik',
                    'dinkes' => 'Dinas Kesehatan Riau'
                ];
                echo Html::encode($sources[$model->customer_source] ?? $model->customer_source);
                ?>
            </td>
        </tr>
    </table>

    <h4>Contact Person</h4>
    <table class="table table-bordered">
        <tr>
            <th>Name</th>
            <td><?= Html::encode($model->pic_name) ?></td>
        </tr>
        <tr>
            <th>Position</th>
            <td><?= Html::encode($model->pic_workroles) ?></td>
        </tr>
        <tr>
            <th>Email</th>
            <td><?= Html::encode($model->pic_email) ?></td>
        </tr>
        <tr>
            <th>Phone</th>
            <td><?= Html::encode($model->pic_phone) ?></td>
        </tr>
    </table>

</div>