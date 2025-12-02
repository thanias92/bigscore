<?php

namespace app\components;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Struktur;
use mdm\admin\components\Helper;

class StrukturHelper
{
  public static function Menu($array, $parent_id = 0)
  {
    $normal = array();
    $temp_array = array();
    foreach ($array as $element) {
      if ($element->parent == $parent_id) {
        $normal['label'] = $element->name;
        $normal['url'] = array($element->menu_url);
        $normal['icon'] = $element->menu_icon;

        if ($normal['url'] != "#") {
          $normal['visible'] = Yii::$app->user->can($normal['url'][0]);
        } else {
          $nromal['visible'] = true;
        }
        $child = self::Menu($array, $element->id);
        if (isset($child)) {
          $normal['items'] = $child;
          foreach ($normal['items'] as $row) {
            if ($row['visible'] == true) {
              $normal['visible'] = true;
            }
          }
        }
        $temp_array[] = $normal;
      }
    }
    return $temp_array;
  }

  public static function MenuManager($array, $parent_id = 0)
  {
    $normal = 0;
    $menu = '';
    foreach ($array as $element) {
      if ($element->parent == $parent_id) {
        $normal++;
        $menu .= '<li class="dd-item dd3-item" data-id="' . $element->id . '"><div class="dd-handle dd3-handle"></div><div class="dd3-content">
				<div class="row">
				<div class="col-sm-6">
                ' . $element->name . ' <b>: ' . $element->jenis . '</b>
				</div>
				<div class="col-sm-6 text-right">';

        $menu .= Html::a(
          'Edit&nbsp;',
          ['update', 'id' => $element->id]
        );
        if (self::MenuChild($element->id) == 0) {
          $menu .= Html::a(
            'Hapus',
            ['delete', 'id' => $element->id],
            [
              'onClick' => 'return confirm("' . Yii::t('app', 'Are You Sure to delete this Data?') . '");',
              'data' => [
                'method' => 'post',
              ],
            ]
          );
        }
        $menu .= '</div></div></div>';
        if (self::MenuManager($array, $element->id) != '') {
          $menu .= self::MenuManager($array, $element->id);
        }
        $menu .= '</li>';
      }
    }
    if ($normal > 0) {
      return '<ol class="dd-list">' . $menu . '</ol>';
    } else {
      return '';
    }
  }

  // public static function MenuJsonSave($array, $parent = null)
  // {
  //   $order = 0;
  //   foreach ($array as $element) {

  //     $order++;
  //     if (isset($element->children)) {
  //       $model = Struktur::findOne($element->id);
  //       $model->order = $order;
  //       $model->parent = $parent;
  //       $model->save();
  //       $child = self::MenuJsonSave($element->children, $element->id);
  //     } else {
  //       $model = Struktur::findOne($element->id);
  //       $model->order = $order;
  //       $model->parent = $parent;
  //       $model->save();
  //     }
  //   }
  // }

  public static function MenuJsonSave($array, $parent = null, $level = 1)
  {
      $order = 0;
      foreach ($array as $element) {
          $order++;
          $model = Struktur::findOne($element->id);
          $model->order = $order;
          $model->parent = $parent;
          $model->level = $level;
          $model->save();
  
          if (isset($element->children)) {
              self::MenuJsonSave($element->children, $element->id, $level + 1);
          }
      }
  }
  

  public static function MenuChild($id)
  {
    $model = Struktur::find()->where(['parent' => $id])->count();
    return $model;
  }


  public static function EmrMenu($array, $id, $root = 0)
  {
    $normal = 0;
    $hasActive = 0;
    $menu = '';
    foreach ($array as $element) {
      if (isset($element['child'])) {
        $child = self::EmrMenu($element['child'], $id, $root + 1);
        if ($child['el'] != '') {
          $normal++;
          $menu .= '<li class="' . ($child['hasActive'] ? 'selected' : '') . '">';
          $options = ['class' => $child['hasActive'] ? 'text-dark active' : 'text-dark'];
          if (isset($element['pjax'])) {
            $options['pjax'] = $element['pjax'];
          }
          if (isset($element['title'])) {
            $options['title'] = $element['title'];
          }
          $menu .= Html::a($element['icon'] . $element['title'], '#', $options);
          $menu .= $child['el'];
          $menu .= '</li>';
        }
      } else {
        if (self::checkRoute($element['url'])) {
          $normal++;
          $menu .= '<li>';
          $break = explode("/", $element['url']);
          $url = [];
          if (count($break) > 0) {
            for ($i = 1; $i < count($break) - 1; $i++) {
              $url[] = $break[$i];
            }
          }
          $url = "/" . implode("/", $url);

          if ($url == '/' . Yii::$app->controller->uniqueId) {
            if ($element['visible'] == true) {
              $options = ['class' => 'text-dark active'];
              if (isset($element['pjax'])) {
                $options['pjax'] = $element['pjax'];
              }
              if (isset($element['title'])) {
                $options['title'] = $element['title'];
              }
              $menu .= Html::a($element['icon'] . $element['title'], [$element['url'], 'id' => $id], $options);
            }
            $hasActive++;
          } else {
            if ($element['visible'] == true) {
              $options = ['class' => 'text-dark'];
              if (isset($element['pjax'])) {
                $options['pjax'] = $element['pjax'];
              }
              if (isset($element['title'])) {
                $options['title'] = $element['title'];
              }
              $menu .= Html::a($element['icon'] . $element['title'], [$element['url'], 'id' => $id],  $options);
            }
          }
          $menu .= '</li>';
        }
      }
    }
    if ($hasActive > 0) {
      $active = true;
    } else {
      $active = false;
    }
    if ($normal > 0) {
      if ($root > 0) {
        return ['el' => '<ul style="' . ($active ? 'display: block;' : 'display: none;') . '">' . $menu . '</ul>', 'hasActive' => $active];
      } else {
        return ['el' => '<ul>' . $menu . '</ul>', 'hasActive' => $active];
      }
    } else {
      return ['el' => '', 'hasActive' => $active];
    }
  }

  public static function checkRoute($url)
  {
    $auth = Yii::$app->authManager;
    $assign = $auth->getAssignments(Yii::$app->user->id);
    $roles = [];
    foreach ($assign as $row) {
      $roles[] = $row->roleName;
    }

    $childs = self::getChild($roles);
    if (in_array($url, $childs)) {
      return true;
    }

    $can = false;
    if (in_array('/*', $childs)) {
      return true;
    }

    while (($pos = strrpos($url, '/')) > 0) {
      $url = substr($url, 0, $pos);
      if (in_array($url . '/*', $childs)) {
        return true;
      }
    }
    return $can;
  }

  public static function getChild($parent)
  {
    $childs = [];
    $itemCHild = \app\models\AuthItemChild::find()->select(['child'])->where(['IN', 'parent', $parent])->asArray()->all();
    foreach ($itemCHild as $row) {
      if (substr($row['child'], 0, 1) == '/') {
        $childs[] = $row['child'];
      } else {
        $childs = array_merge($childs, self::getChild($row['child']));
      }
    }
    return $childs;
  }
}
