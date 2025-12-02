<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/themes/favicon.png')]);
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Html::encode(Yii::$app->name . ' :: ' . ($this->title ?? '')) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= Url::base() ?>/themes/css/style.css">
    <?php $this->head() ?>
</head>

<body class="bg-light d-flex h-100">
    <?php $this->beginBody() ?>

    <aside class="sidebar bg-white shadow-sm" style="width: 240px; flex-shrink: 0;">
        <div class="sidebar-header p-3">
            <a href="<?= Url::to(['/site/index']) ?>" class="navbar-brand d-block">
                <img src="<?= Url::base() ?>/theme/images/logo-bigscore.png" height="30" alt="BIGS core">
            </a>
            <span class="text-muted">Menu</span>
        </div>
        <div class="sidebar-body p-2">
            <?= $this->render('shared/side_menu_sales') ?>
        </div>
        <div class="sidebar-footer p-3">
            <i class="bi bi-box-arrow-left me-2"></i> Logout
        </div>
    </aside>

    <main class="main-content" style="margin-left: 240px; flex-grow: 1;">
        <header class="bg-white shadow-sm px-4 py-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0"><?= Html::encode($this->title) ?></h5>
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <input type="text" class="form-control form-control-sm" placeholder="Search...">
                </div>
                <a href="#" class="btn btn-primary btn-sm">+ Add</a>
                <div class="ms-3">
                    <span class="me-2">John Doe</span>
                    <span class="text-muted">(Sales)</span>
                </div>
                <div class="ms-3">
                    <i class="bi bi-person-circle" style="font-size: 1.5rem;"></i>
                </div>
            </div>
        </header>

        <div class="container-fluid p-4">
            <?= $content ?>
        </div>
    </main>

    <div id="modal-container"></div>

    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>