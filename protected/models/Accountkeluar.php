<?php

namespace app\models;

use Yii;
use app\traits\AuditTrailTrait;

/**
 * This is the model class for table "accountkeluar".
 *
 * @property int $id
 * @property string $code
 * @property string $akun
 * @property string $penggunaan
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 */
class Accountkeluar extends \yii\db\ActiveRecord
{
  use AuditTrailTrait;
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'accountkeluar';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['code', 'akun', 'penggunaan'], 'required'],
      [['parent_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
      [['created_at', 'updated_at', 'deleted_at'], 'safe'],
      [['code', 'akun', 'penggunaan'], 'string', 'max' => 255],
    ];
  }

  public function attributeLabels()
  {
    return [
      'id'         => 'ID',
      'parent_id'  => 'Akun Induk',
      'code'       => 'Kode',
      'akun'       => 'Nama Akun',
      'penggunaan' => 'Penggunaan',
      'created_at' => 'Dibuat',
      'updated_at' => 'Diubah',
    ];
  }

  public function getAkunPengeluaran()
  {
    return self::find()->where(['penggunaan' => 'pengeluaran']);
  }

  // Akun untuk pemasukan
  public function getAkunPemasukan()
  {
    return self::find()->where(['penggunaan' => 'pemasukan']);
  }
  public function getParent()
  {
    return $this->hasOne(Accountkeluar::class, ['id' => 'parent_id']);
  }

  public function getChildren()
  {
    return $this->hasMany(Accountkeluar::class, ['parent_id' => 'id']);
  }

  public static function listPemasukan()
  {
      return self::find()
          ->where(['ilike', 'penggunaan', 'pemasukan'])
          ->andWhere(['or',
              ['like', 'code', '4-% ', false],
              ['like', 'code', '7-%', false],
          ])
          ->orderBy(['akun' => SORT_ASC])
          ->all();
  }
}
