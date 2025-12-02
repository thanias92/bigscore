<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
//kanza push ulang untuk hosting

$this->title = $deals_id;
$this->params['breadcrumbs'][] = ['label' => 'Implementation', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
$this->registerCssFile('https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css');
?>
<style>
    .bg-button {
        background: #27465E;
        color: white;
    }

    .bg-button:hover {
        background: #0e314c;
    }
</style>
<div class="implementation-view flex flex-col justify-center items-center">
    <div class="w-full flex flex-row justify-end">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="text-xl mb-2" style="color: #27465E">
        MULAI IMPLEMENTASI?
    </div>
    <p class="text-xs text-gray-700">
        Anda yakin ingin mengimplementasi project ini?
    </p>
    <a href="<?php print(Url::to(['proses', 'deals_id' => $deals_id])) ?>" class="flex flex-row justify-center items-center rounded-lg bg-button px-4 py-2 text-sm ">
        YA, MULAI!
    </a>

</div>