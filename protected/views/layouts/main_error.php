<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\assets\AppAsset;

AppAsset::register($this);
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
  <?php $this->head() ?>
</head>

<body>
  <?php $this->beginBody() ?>
  <div class="container">
    <?= $content ?>
    <div class="text-center">
      <a href="<?= Url::home() ?>" class="btn btn-sm btn-primary">
        <i class="fas fa-home"></i>
        Kembali Ke Aplikasi
      </a>
    </div>
    <div class="text-center">
      Copyright &copy; Emesys.id <?= date("Y") ?>
    </div>
  </div>
  <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>