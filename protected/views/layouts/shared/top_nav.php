<?php

use yii\helpers\Url;
use yii\helpers\Html;


?>

<nav class="nav navbar navbar-expand-xl navbar-light iq-navbar">
  <div class="container-fluid navbar-inner">
    <a href="./index.html" class="navbar-brand">

      <!--Logo start-->
      <div class="logo-main">
        <img class="logo-normal img-fluid" src="<?= Url::base(true); ?>/themes/images/logo.png" height="30" alt="logo">
        <img class="logo-normal dark-normal img-fluid" src="<?= Url::base(true); ?>/themes/images/logo-dark.png" height="30" alt="logo">
        <img class="logo-normal white-normal img-fluid" src="<?= Url::base(true); ?>/themes/images/logo-white.png" height="30" alt="logo">
        <img class="logo-mini img-fluid" src="<?= Url::base(true); ?>/themes/images/logo-mini.png" height="30" alt="logo">
        <img class="logo-mini dark-mini img-fluid" src="<?= Url::base(true); ?>/themes/images/logo-mini-dark.png" height="30" alt="logo">
        <img class="logo-mini white-mini img-fluid" src="<?= Url::base(true); ?>/themes/images/logo-mini-white.png" height="30" alt="logo">
      </div>
      <!--logo End-->
    </a>
    <div class="sidebar-toggle" data-toggle="sidebar" data-active="true">
      <i class="icon d-flex">
        <svg class="icon-20" width="20" viewBox="0 0 24 24">
          <path fill="currentColor" d="M4,11V13H16L10.5,18.5L11.92,19.92L19.84,12L11.92,4.08L10.5,5.5L16,11H4Z" />
        </svg>
      </i>
    </div>
    <div class="d-flex align-items-center justify-content-between product-offcanvas">
      <div class="offcanvas offcanvas-end shadow-none iq-product-menu-responsive" tabindex="-1" id="offcanvasBottomNav">
        <div class="offcanvas-body">
          <?= $this->render('main_menu') ?>
        </div>
      </div>
    </div>
    <div class="d-flex align-items-center">
      <button id="navbar-toggle" class="navbar-toggler px-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-btn">
          <span class="navbar-toggler-icon"></span>
        </span>
      </button>
    </div>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="mb-2 navbar-nav ms-auto align-items-center navbar-list mb-lg-0">
        <li class="nav-item dropdown me-0 me-xl-3">
          <div class="d-flex align-items-center mr-2 iq-font-style" role="group" aria-label="First group" data-setting="radio">
            <input type="radio" class="btn-check" name="theme_font_size" value="theme-fs-sm" id="font-size-sm">
            <label for="font-size-sm" class="btn btn-border border-0 btn-icon btn-sm" data-bs-toggle="tooltip" title="Font size 14px" data-bs-placement="bottom">
              <span class="mb-0 h6" style="color: inherit !important;">A</span>
            </label>
            <input type="radio" class="btn-check" name="theme_font_size" value="theme-fs-md" id="font-size-md">
            <label for="font-size-md" class="btn btn-border border-0 btn-icon" data-bs-toggle="tooltip" title="Font size 16px" data-bs-placement="bottom">
              <span class="mb-0 h4" style="color: inherit !important;">A</span>
            </label>
            <input type="radio" class="btn-check" name="theme_font_size" value="theme-fs-lg" id="font-size-lg">
            <label for="font-size-lg" class="btn btn-border border-0 btn-icon" data-bs-toggle="tooltip" title="Font size 18px" data-bs-placement="bottom">
              <span class="mb-0 h1" style="color: inherit !important;">A</span>
            </label>
          </div>
        </li>
        <li class="nav-item dropdown iq-responsive-menu d-block d-xl-none">
          <div class="btn btn-sm px-0 border-0" id="navbarDropdown-search-11" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <circle cx="11.7669" cy="11.7666" r="8.98856" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></circle>
              <path d="M18.0186 18.4851L21.5426 22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
          </div>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown-search-11" style="width: 25rem;">
            <li class="px-3 py-0">
              <div class="form-group input-group mb-0">
                <input type="text" class="form-control" placeholder="Search...">
                <span class="input-group-text">
                  <svg class="icon-20" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="11.7669" cy="11.7666" r="8.98856" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></circle>
                    <path d="M18.0186 18.4851L21.5426 22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                  </svg>
                </span>
              </div>
            </li>
          </ul>
        </li>

        <li class="nav-item iq-full-screen d-none d-xl-block" id="fullscreen-item">
          <a href="#" class="nav-link" id="btnFullscreen" data-bs-toggle="dropdown">
            <div class="icon nav-list-icon">
              <span class="btn-inner">
                <svg class="normal-screen icon-20" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M18.5528 5.99656L13.8595 10.8961" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                  <path d="M14.8016 5.97618L18.5524 5.99629L18.5176 9.96906" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                  <path d="M5.8574 18.896L10.5507 13.9964" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                  <path d="M9.60852 18.9164L5.85775 18.8963L5.89258 14.9235" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
                <svg class="full-normal-screen d-none icon-20" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M13.7542 10.1932L18.1867 5.79319" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                  <path d="M17.2976 10.212L13.7547 10.1934L13.7871 6.62518" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                  <path d="M10.4224 13.5726L5.82149 18.1398" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                  <path d="M6.74391 13.5535L10.4209 13.5723L10.3867 17.2755" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
              </span>
            </div>
          </a>
        </li>
        <li class="nav-item dropdown" id="itemdropdown1">
          <a class="py-0 nav-link d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <div class="icon-40">
              <span class="btn-inner d-inline-block position-relative">
                <img src="<?= Url::base(true); ?>/themes/images/logo-mini-dark.png" class="img-fluid rounded-circle object-fit-cover" alt="icon">
                <span class="bg-success p-1 rounded-circle position-absolute end-0 bottom-0 border border-3 border-white"></span>
              </span>
            </div>
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
            <li>
              <a class="dropdown-item" href="<?= Url::to(['/profile']); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear me-2" viewBox="0 0 16 16">
                  <path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492M5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0" />
                  <path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115z" />
                </svg>
                Profile
              </a>
            </li>

            <!-- === TAMBAHKAN BLOK KODE INI === -->
            <?php if (Yii::$app->user->can('Sales Manager')) : ?>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li>
              <li>
                <a class="dropdown-item" href="<?= Url::to(['/sales/sales-settings/index']) ?>">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bullseye me-2" viewBox="0 0 16 16">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                    <path d="M8 13A5 5 0 1 1 8 3a5 5 0 0 1 0 10m0 1A6 6 0 1 0 8 2a6 6 0 0 0 0 12" />
                    <path d="M8 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6m0 1a4 4 0 1 0 8 4 4 0 0 0 0-8" />
                    <path d="M9.5 8a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0" />
                  </svg>
                  Sales Target Settings
                </a>
              </li>
            <?php endif; ?>
            <!-- === AKHIR BLOK KODE TAMBAHAN === -->

            <!-- LINK TRIGGER  (tidak redirect, hanya AJAX) -->
            <li>
              <hr class="dropdown-divider">
            </li>
            <li>
            <li class="nav-item">
              <a class="dropdown-item" href="<?= Url::to(['/vendorfinance/pengaturanakun/index']) ?>">
                <i class="bi bi-gear"></i> Pengaturan Akun
              </a>
            </li>

            <li>
              <hr class="dropdown-divider">
            </li>
            <li>
              <a href="<?= Url::to(['/default/logout']); ?>" class="dropdown-item">
                <i class="fas fa-sign-out-alt"></i> Logout
              </a>
            </li>

          </ul>

        </li>
      </ul>
    </div>
  </div>
</nav> <!--Nav End-->