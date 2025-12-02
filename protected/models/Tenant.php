<?php

namespace app\models;

use Yii;
use app\traits\AuditTrailTrait;

/**
* This is the model class for table "tenant".
*
  * @property int $id
  * @property string $uuid
  * @property string $code
  * @property string $name
  * @property string $email
  * @property string $phone
  * @property string $address
  * @property string $host
  * @property bool|null $status
  * @property int $created_at
  * @property int|null $updated_at
  * @property int $created_by
  * @property int|null $updated_by
  * @property int $deleted_at
  * @property int|null $deleted_by
*/
class Tenant extends \yii\db\ActiveRecord
{
    use AuditTrailTrait;
    /**
    * {@inheritdoc}
    */
    public static function tableName()
    {
        return 'tenant';
    }

    /**
    * {@inheritdoc}
    */
    public function rules()
    {
        return [
                    [['uuid', 'code', 'name', 'email', 'phone', 'address', 'host', 'created_at', 'created_by'], 'required'],
                    [['address'], 'string'],
                    [['status'], 'boolean'],
                    [['created_at', 'updated_at', 'created_by', 'updated_by', 'deleted_at', 'deleted_by'], 'default', 'value' => null],
                    [['created_at', 'updated_at', 'created_by', 'updated_by', 'deleted_at', 'deleted_by'], 'integer'],
                    [['uuid', 'name', 'email', 'phone'], 'string', 'max' => 128],
                    [['code'], 'string', 'max' => 20],
                    [['host'], 'string', 'max' => 255],
                ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            Yii::info('Created At: ' . $this->created_at, __METHOD__);
            Yii::info('Created By: ' . $this->created_by, __METHOD__);
            return true;
        }
        return false;
    }

    /**
    * {@inheritdoc}
    */
    public function attributeLabels()
    {
        return [
          'id' => 'ID',
          'uuid' => 'Uuid',
          'code' => 'Code',
          'name' => 'Name',
          'email' => 'Email',
          'phone' => 'Phone',
          'address' => 'Address',
          'host' => 'Host',
          'status' => 'Status',
          'created_at' => 'Created At',
          'updated_at' => 'Updated At',
          'created_by' => 'Created By',
          'updated_by' => 'Updated By',
          'deleted_at' => 'Deleted At',
          'deleted_by' => 'Deleted By',
        ];
    }
}
