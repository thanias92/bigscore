<?php

namespace app\modules\core;

use app\models\Menu;

/**
 * core module definition class
 */
class Module extends \yii\base\Module
{
  /**
   * {@inheritdoc}
   */
  public $controllerNamespace = 'app\modules\core\controllers';
  /**
   * {@inheritdoc}
   */
  public function beforeAction($action)
  {
    parent::beforeAction($action);
    $this->view->params['menu'] = [
      [
        'url' => '/core/route',
        'label' => 'Route',
        'icon' => '',
        'items' => [],
        'options' => ['class' => 'nav-item dropdown'],
      ],
      [
        'url' => '/core/rule',
        'label' => 'Rules',
        'icon' => '',
        'items' => [],
        'options' => ['class' => 'nav-item dropdown'],
      ],
      [
        'url' => '/core/role',
        'label' => 'Role',
        'icon' => '',
        'items' => [],
        'options' => ['class' => 'nav-item dropdown'],
      ],
      [
        'url' => '/core/user',
        'label' => 'User',
        'icon' => '',
        'items' => [],
        'options' => ['class' => 'nav-item dropdown'],
      ],
      [
        'url' => '/core/assignment',
        'label' => 'Assignment',
        'icon' => '',
        'items' => [],
        'options' => ['class' => 'nav-item dropdown'],
      ],
      [
        'url' => '/core/menu',
        'label' => 'Menu',
        'icon' => '',
        'items' => [],
        'options' => ['class' => 'nav-item dropdown'],
      ],
    ];
    return true;
  }
}
