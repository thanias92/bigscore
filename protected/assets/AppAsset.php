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
class AppAsset extends AssetBundle
{
  public $basePath = '@webroot';
  public $baseUrl = '@web';
  public $css = [
    'themes/fonts/roboto.css',
    'themes/css/core/libs.min.css',
    'themes/vendor/flaticon/css/flaticon.css',
    'themes/vendor/font-awesome/css/all.min.css',
    'themes/css/kivicare.min.css?v=1.2.0',
    'themes/vendor/sweetalert2/dist/sweetalert2.min.css',
    'themes/vendor/flatpickr/dist/flatpickr.min.css',
    'themes/vendor/iziToast/iziToast.min.css',
    'themes/css/custom.css',
    'themes/css/tabulator_custom.css',
    'themes/plugins/simple-loading/css/simple-loading.min.css',
  ];
  public $js = [
    'themes/vendor/sweetalert2/dist/sweetalert2.min.js',
    'themes/vendor/flatpickr/dist/flatpickr.min.js',
    'themes/vendor/lodash/lodash.min.js',
    'themes/vendor/iziToast/iziToast.min.js',
    'themes/js/iqonic-script/utility.min.js',
    'themes/js/iqonic-script/setting.min.js',
    'themes/js/setting-init.js',
    'themes/js/core/external.min.js',
    'themes/js/kivicare.js?v=1.2.0',
    'themes/js/kivicare-advance.js?v=1.2.0',
    'themes/js/sidebar.js?v=1.2.0',
    'themes/js/modal.js',
    'app/js/general_function.js',
    'app/js/modal_function.js',
    'themes/plugins/simple-loading/js/simple-loading.min.js',
  ];
  public $depends = [
    'yii\web\YiiAsset',
    'yii\bootstrap5\BootstrapAsset'
  ];
}
