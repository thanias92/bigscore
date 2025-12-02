<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Feedback */

$this->title = 'Update Feedback: ' . $model->id_feedback;
?>
<div class="feedback-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
