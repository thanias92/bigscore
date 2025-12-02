<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "customer_history".
 *
 * @property int $id
 * @property int $customer_id
 * @property string|null $field_changed
 * @property string|null $old_value
 * @property string|null $new_value
 * @property string $activity_type
 * @property string $description
 * @property int|null $created_by
 * @property string|null $created_at
 *
 * @property Customer $customer
 * @property User $createdBy
 */
class CustomerHistory extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'customer_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['customer_id', 'activity_type', 'description'], 'required'],
            [['customer_id', 'created_by'], 'integer'],
            [['old_value', 'new_value', 'description'], 'string'],
            [['created_at'], 'safe'],
            [['field_changed', 'activity_type'], 'string', 'max' => 255],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::class, 'targetAttribute' => ['customer_id' => 'customer_id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customer_id' => 'Customer ID',
            'field_changed' => 'Field Changed',
            'old_value' => 'Old Value',
            'new_value' => 'New Value',
            'activity_type' => 'Activity Type',
            'description' => 'Description',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[Customer]].
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::class, ['customer_id' => 'customer_id']);
    }

    /**
     * Gets query for [[User]] who created the history record.
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function getFormattedCreatedAt($format = 'd-m-Y')
    {
        return Yii::$app->formatter->asDate($this->created_at, 'php:' . $format);
    }

    public function getCreatedByUserName()
    {
        if ($this->createdBy) {
            return $this->createdBy->username;
        }
        return 'System';
    }
}
