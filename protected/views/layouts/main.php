<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use yii\helpers\Url;
use yii\bootstrap5\Html;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/themes/favicon.png')]);
?>
<?php $this->beginPage() ?>
<!doctype html>
<html lang="<?= Yii::$app->language ?>" dir="ltr" style="--bs-table-bg: white;">


<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="google_font_api" content="AIzaSyBG58yNdAjc20_8jAvLNSVi9E4Xhwjau_k">
  <title><?= ($this->title != '' ? Html::encode(Yii::$app->name . ' :: ' . $this->title) : Yii::$app->name) ?></title>
  <!-- Import Inter font -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <!-- Config Options -->
  <meta name="setting_options" content='{"saveLocal":"sessionStorage","storeKey":"huisetting","setting":{"app_name":{"value":"Emesys"},"theme_scheme_direction":{"value":"ltr"},"theme_scheme":{"value":"light"},"theme_style_appearance":{"value":["theme-default"]},"theme_color":{"colors":{"--{{prefix}}primary":"#2d437f","--{{prefix}secondary":"#f68685"},"value":"custom"},"theme_transition":{"value":"theme-with-animation"},"theme_font_size":{"value":"theme-fs-sm"},"page_layout":{"value":"container-fluid"},"header_navbar":{"value":"navs-sticky"},"header_banner":{"value":"default"},"card_color":{"value":"card-default"},"sidebar_color":{"value":"sidebar-white"},"sidebar_type":{"value":["sidebar-mini"]},"sidebar_menu_style":{"value":"navs-rounded-all"},"footer":{"value":"default"},"body_font_family":{"value":"Inter"},"heading_font_family":{"value":"Inter"}}}'>
  <?php $this->head() ?>
</head>

<body class="">
  <?= $this->render("flash_notification") ?>
  <?php $this->beginBody() ?>
  <!-- loader Start -->
  <div id="loading">
    <div class="loader simple-loader">
      <div class="loader-body">
        <img src="<?= Url::base(true); ?>/themes/images/loader.png" alt="loader" class="light-loader img-fluid " width="200">
      </div>
    </div>
  </div>

  <!-- loading -->
  <div style="z-index: 1000; display:none;" class="page-spinner bg-light w-100 h-100 justify-content-center align-items-center position-absolute top-50 start-50 translate-middle">
    <div class="spinner-border text-info" style="width: 3rem; height: 3rem;" role="status"></div>
  </div>
  <!-- end loading -->
  <!-- loader END -->

  <aside class="sidebar sidebar-base sidebar-white sidebar-default navs-rounded-all sidebar-mini" id="first-tour" data-toggle="main-sidebar" data-sidebar="responsive">
    <div class="sidebar-header d-flex align-items-center justify-content-start position-relative">
      <a href="#" class="navbar-brand">

        <!--Logo start-->
        <div class="logo-main" style="display:flex;">
          <img class="logo-normal img-fluid" src="<?= Url::base(true); ?>/themes\images\logo-bigscore.png" width="120" height="60" alt="logo">
          <img class="logo-normal dark-normal img-fluid" src="<?= Url::base(true); ?>/themes/images/logo-dark.png" height="30" alt="logo">
          <img class="logo-normal white-normal img-fluid" src="<?= Url::base(true); ?>/themes\images\logo-bigscore.png" height="30" alt="logo">
          <img class="logo-mini img-fluid" src="<?= Url::base(true); ?>/themes/images/logo-mini.png" height="30" alt="logo">
          <img class="logo-mini dark-mini img-fluid" src="<?= Url::base(true); ?>/themes/images/logo-mini-dark.png" height="30" alt="logo">
          <img class="logo-mini white-mini img-fluid" src="<?= Url::base(true); ?>/themes/images/logo-mini-white.png" height="30" alt="logo">
        </div>
        <!--logo End-->
      </a>
      <div class="sidebar-toggle" data-toggle="sidebar" data-active="true">
        <i class="icon">
          <svg class="icon-16 icon-arrow" width="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M15.5 19L8.5 12L15.5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
          </svg>
        </i>
      </div>
    </div>
    <div class="sidebar-body pt-0 data-scrollbar">
      <div class="sidebar-list">
        <?= $this->render('shared/side_menu') ?>
      </div>
    </div>
  </aside>
  <main class="main-content">
    <div class="position-relative ">
      <!--Nav Start-->
      <?= $this->render('shared/top_nav') ?>
      <!--Nav End-->
    </div>
    <div class="content-inner container-fluid pb-0" id="page_layout">
      <?= $content ?>
    </div>
  </main>
  <!-- Wrapper End-->

  <!-- Modal Default -->
  <div class="modal fade" id="modal_df" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <!-- loading -->
        <div style="z-index: 1000; display:none;" class="modal-spinner bg-light w-100 h-100 justify-content-center align-items-center position-absolute top-50 start-50 translate-middle">
          <div class="spinner-border text-info" style="width: 3rem; height: 3rem;" role="status"></div>
        </div>
        <!-- end loading -->
        <div class="modal-header">
          <h5 class="modal-title">Modal title</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body"></div>

      </div>
    </div>
  </div>

  <!-- Modal Small -->
  <div class="modal fade" id="modal_sm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <!-- loading -->
        <div style="z-index: 1000; display:none;" class="modal-spinner bg-light w-100 h-100 justify-content-center align-items-center position-absolute top-50 start-50 translate-middle">
          <div class="spinner-border text-info" style="width: 3rem; height: 3rem;" role="status"></div>
        </div>
        <!-- end loading -->
        <div class="modal-header">
          <h5 class="modal-title">Modal title</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body"></div>

      </div>
    </div>
  </div>

  <!-- Modal Large -->
  <div class="modal fade" id="modal_lg" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <!-- loading -->
        <div style="z-index: 1000; display:none;" class="modal-spinner bg-light w-100 h-100 justify-content-center align-items-center position-absolute top-50 start-50 translate-middle">
          <div class="spinner-border text-info" style="width: 3rem; height: 3rem;" role="status"></div>
        </div>
        <!-- end loading -->
        <div class="modal-header">
          <h5 class="modal-title">Modal title</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body"></div>

      </div>
    </div>
  </div>

  <!-- Modal Extra Large -->
  <div class="modal fade modal_xl" id="modal_xl" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog">
    <div class="modal-dialog modal-xl" style="max-width: 90%;">

      <div class="modal-content">
        <!-- loading -->
        <div style="z-index: 1000; display:none;" class="modal-spinner bg-light w-100 h-100 justify-content-center align-items-center position-absolute top-50 start-50 translate-middle">
          <div class="spinner-border text-info" style="width: 3rem; height: 3rem;" role="status"></div>
        </div>
        <!-- end loading -->
        <div class="modal-header">
          <h5 class="modal-title">Modal title</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body"></div>
      </div>
    </div>
  </div>
  <?php $this->endBody() ?>

</body>

</html>
<?php $this->endPage() ?>