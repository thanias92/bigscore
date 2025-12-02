<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\helpers\Url;
use app\assets\LoginAsset;

LoginAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
  <meta charset="<?= Yii::$app->charset ?>">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php $this->registerCsrfMetaTags() ?>
  <title><?= ($this->title ? (Html::encode($this->title) . ' - ') : '') . Yii::$app->name ?></title>
  <link rel="shortcut icon" href="<?= Url::base(true); ?>/themes/favicon.png" type="image/x-icon" />
  <meta name="setting_options" content='{&quot;saveLocal&quot;:&quot;sessionStorage&quot;,
  &quot;storeKey&quot;:&quot;huisetting&quot;,
  &quot;setting&quot;:{&quot;app_name&quot;:{&quot;value&quot;:&quot;Emesys&quot;},
  &quot;theme_scheme_direction&quot;:{&quot;value&quot;:&quot;ltr&quot;},
  &quot;theme_scheme&quot;:{&quot;value&quot;:&quot;light&quot;},&quot;theme_style_appearance&quot;:{&quot;value&quot;:[&quot;theme-default&quot;]},&quot;theme_color&quot;:{&quot;colors&quot;:{&quot;--{{prefix}}primary&quot;:&quot;#2d437f&quot;,&quot;
  --{{prefix}secondary&quot;:&quot;#f68685&quot;,&quot;--
  {{prefix}}secondary&quot;:&quot;#f68685&quot;},&quot;value&quot;:&quot;custom&quot;},&quot;theme_transition&quot;:{&quot;value&quot;:&quot;theme-with-animation&quot;},&quot;theme_font_size&quot;:{&quot;value&quot;:&quot;theme-fs-sm&quot;},&quot;page_layout&quot;:{&quot;value&quot;:&quot;container-fluid&quot;},&quot;header_navbar&quot;:{&quot;value&quot;:&quot;navs-sticky&quot;},&quot;header_banner&quot;:{&quot;value&quot;:&quot;default&quot;},&quot;card_color&quot;:{&quot;value&quot;:&quot;card-default&quot;},&quot;sidebar_color&quot;:{&quot;value&quot;:&quot;#fefeff&quot;},&quot;sidebar_type&quot;:{&quot;value&quot;:[&quot;sidebar-soft&quot;]},&quot;sidebar_menu_style&quot;:{&quot;value&quot;:&quot;sidebar-defaultnavs-rounded-all&quot;},&quot;footer&quot;:{&quot;value&quot;:&quot;default&quot;},&quot;body_font_family&quot;:{&quot;value&quot;:&quot;Poppins&quot;},&quot;heading_font_family&quot;:{&quot;value&quot;:&quot;Poppins&quot;}}}'>
  <meta name="google_font_api" content="AIzaSyBG58yNdAjc20_8jAvLNSVi9E4Xhwjau_k">
  <?php $this->head() ?>
</head>

<body style="background-color: #eff7f8;">
  <?php $this->beginBody() ?>
  <!-- loader Start -->
  <div id="loading">
    <div class="loader simple-loader">
      <div class="loader-body">
        <img src="<?= Url::base(true); ?>/themes/images/loader.png" alt="loader" class="light-loader img-fluid " width="200">
      </div>
    </div>
  </div>
  <!-- loader END -->
  <?= $content ?>
  <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>