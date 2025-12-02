<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "salesman_profile".
 *
 * @property int $id
 * @property int $user_id
 * @property int $visit_target
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 *
 * @property User $user
 */
class SalesmanProfile extends ActiveRecord
{
  // Kita tidak perlu AuditTrailTrait jika sudah mendefinisikan behaviors secara manual
  // use AuditTrailTrait; 

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return '{{%salesman_profile}}';
  }

  /**
   * Menambahkan behaviors untuk mengisi created_at, updated_at secara otomatis.
   * BlameableBehavior (created_by, updated_by) bisa ditambahkan jika AuditTrailTrait tidak digunakan.
   */
  public function behaviors()
  {
    return [
      [
        'class' => TimestampBehavior::class,
        'attributes' => [
          ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
          ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
        ],
      ],
      // Jika AuditTrailTrait Anda tidak menangani created_by/updated_by,
      // aktifkan BlameableBehavior di bawah ini dengan menghapus komentarnya.
      /*
            [
                'class' => \yii\behaviors\BlameableBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_by', 'updated_by'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_by'],
                ],
            ],
            */
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['user_id', 'visit_target'], 'required'],
      [['user_id', 'visit_target', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
      [['visit_target'], 'default', 'value' => 20],
      [['user_id'], 'unique'],
      [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => 'ID',
      'user_id' => 'User',
      'visit_target' => 'Visit Target',
      'created_at' => 'Created At',
      'updated_at' => 'Updated At',
      'created_by' => 'Created By',
      'updated_by' => 'Updated By',
    ];
  }

  /**
   * Gets query for [[User]].
   *
   * @return \yii\db\ActiveQuery
   */
  public function getUser()
  {
    return $this->hasOne(User::class, ['id' => 'user_id']);
  }
}
