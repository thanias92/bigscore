<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class ContractHistory extends ActiveRecord
{
    public static function tableName()
    {
        return 'contract_history';
    }

    public function rules()
    {
        return [
            [['contract_id', 'activity_type', 'description'], 'required'],
            [['contract_id', 'created_by'], 'integer'],
            [['description', 'old_value', 'new_value'], 'string'],
            [['created_at'], 'safe'],
            [['activity_type', 'field_changed'], 'string', 'max' => 255],
            [['contract_id'], 'exist', 'skipOnError' => true, 'targetClass' => Contract::class, 'targetAttribute' => ['contract_id' => 'contract_id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'contract_id' => 'Contract ID',
            'activity_type' => 'Activity Type',
            'description' => 'Description',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
        ];
    }

    public function getContract()
    {
        return $this->hasOne(Contract::class, ['contract_id' => 'contract_id']);
    }

    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }
}
