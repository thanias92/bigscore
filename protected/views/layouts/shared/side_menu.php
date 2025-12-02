<?php

use yii\helpers\Json;
use app\widgets\Menu;
use app\components\mdm\MenuHelper;

$items = MenuHelper::getAssignedMenu(
  Yii::$app->user->id,
  null,
  function ($menu) {
    $urlParams = [$menu['route']];
    $json = $menu['params'];
    if (!empty($json)) {
      $param = Json::decode($json, $asArray = true);
      foreach ($param as $key => $value) {
        $urlParams[$key] = $value;
      }
    }
    return [
      'id' => $menu['id'],
      'label' => $menu['name'],
      'url' => $urlParams,
      'options' => ['class' => 'nav-item'],
      'icon' => $menu['data'],
      'order' => $menu['order'],
      'items' => $menu['children'],
    ];
  }
);

echo Menu::widget(
  [
    'options' => ['class' => 'navbar-nav iq-main-menu justyfy-start', 'id' => 'sidebar-menu'],
    'activeCssClass' => 'active',
    'items' => $items,
  ]
);
