<?php

namespace app\assets;

use yii\web\AssetBundle;

class TabulatorAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'libs/tabulator/css/tabulator_bootstrap5.min.css',
    ];
    public $js = [
        'libs/tabulator/js/tabulator.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
    ];
}
