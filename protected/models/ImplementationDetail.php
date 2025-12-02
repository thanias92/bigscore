<?php

namespace app\models;

use Yii;
use app\traits\AuditTrailTrait;

class ImplementationDetail extends \yii\db\ActiveRecord
{
    public $label_deals;
    public $customer_name;

    use AuditTrailTrait;

    public static function tableName()
    {
        return 'implementation_detail';
    }

    public function rules()
    {
        return [
            // [['completion_date', 'notes', 'duration', 'created_by', 'updated_by', 'deleted_by', 'deleted_at', 'label_deals', 'nama_customer'], 'default', 'value' => null],
            // [['line_progress'], 'default', 'value' => 0],
            // [['activity_title', 'activity', 'detail', 'start_date', 'pic_aktivitas', 'status'], 'required'],
            // [['start_date', 'completion_date', 'duration', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
            // [['notes'], 'string'],
            // [['line_progress', 'created_by', 'updated_by', 'deleted_by'], 'default', 'value' => null],
            // [['line_progress', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            // [['activity_title', 'activity', 'detail', 'pic_aktivitas', 'status'], 'string', 'max' => 255],
        ];
    }


    public function attributeLabels()
    {
        return [];
    }
}
