<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "menu".
 *
 * @property int $id
 * @property string $name
 * @property int $parent
 * @property string $route
 * @property int $order
 * @property string $data
 *
 * @property Menu $parent0
 * @property Menu[] $menus
 */
class Menu extends \yii\db\ActiveRecord
{
  public $json_tree;
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'menu';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['name'], 'required'],
      [['parent', 'order'], 'default', 'value' => null],
      [['parent', 'order'], 'integer'],
      [['data', 'deskripsi'], 'string'],
      [['name', 'route'], 'string', 'max' => 255],
      [['parent'], 'exist', 'skipOnError' => true, 'targetClass' => Menu::className(), 'targetAttribute' => ['parent' => 'id']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => 'ID',
      'name' => 'Name',
      'parent' => 'Parent',
      'route' => 'Route',
      'order' => 'Order',
      'data' => 'Icon HTML Tag',
      'params' => 'Parameter',
      'deskripsi' => 'Deskripsi',
    ];
  }

  /**
   * @return \yii\db\ActiveQuery
   */
  public function getParent0()
  {
    return $this->hasOne(Menu::className(), ['id' => 'parent']);
  }

  /**
   * @return \yii\db\ActiveQuery
   */
  public function getMenus()
  {
    return $this->hasMany(Menu::className(), ['parent' => 'id']);
  }
}
