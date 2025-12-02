<?php

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class LoginAsset extends AssetBundle
{
  public $basePath = '@webroot';
  public $baseUrl = '@web';
  public $css = [
    'themes/css/core/libs.min.css',
    'themes/vendor/flaticon/css/flaticon.css',
    'themes/vendor/font-awesome/css/all.min.css',
    'themes/css/kivicare.min.css?v=1.2.0',
    'themes/css/custom.min.css?v=1.2.0',
    'themes/css/customizer.min.css?v=1.2.0',
  ];
  public $js = [
    'themes/vendor/lodash/lodash.min.js',
    'themes/js/iqonic-script/utility.min.js',
    'themes/js/iqonic-script/setting.min.js',
    'themes/js/setting-init.js',
    'themes/js/kivicare.js?v=1.2.0',
    'themes/js/kivicare-advance.js?v=1.2.0',
  ];
  public $depends = [
    'yii\web\YiiAsset',
    'yii\bootstrap5\BootstrapAsset'
  ];
}
