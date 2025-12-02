<?php

use yii\bootstrap5\Nav;

if (isset($this->params['menu'])) {
  echo Nav::widget([
    'items' => array_map(function ($item) {
      return [
        'label' => $item['label'],
        'url' => [$item['url']],
        'icon' => $item['icon'],
        'items' => $item['items'],
        'options' => $item['options'],
      ];
    }, $this->params['menu']),
    'options' => ['class' => 'nav-tabs iq-nav-menu list-unstyled'], // set this to nav-tabs to get tab-styled navigation
  ]);
}
?>
