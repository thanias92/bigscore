<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "quotation_history".
 *
 * @property int $id
 * @property int $quotation_id
 * @property string|null $field_changed
 * @property string|null $old_value
 * @property string|null $new_value
 * @property string $activity_type
 * @property string $description
 * @property int|null $created_by
 * @property string|null $created_at
 *
 * @property Quotation $quotation
 */
class QuotationHistory extends ActiveRecord
{
    public static function tableName()
    {
        return 'quotation_history';
    }

    public function rules()
    {
        return [
            [['quotation_id', 'activity_type', 'description'], 'required'],
            [['quotation_id', 'created_by'], 'integer'],
            [['old_value', 'new_value', 'description'], 'string'],
            [['created_at'], 'safe'],
            [['field_changed', 'activity_type'], 'string', 'max' => 255],
            [['quotation_id'], 'exist', 'skipOnError' => true, 'targetClass' => Quotation::class, 'targetAttribute' => ['quotation_id' => 'quotation_id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'quotation_id' => 'Quotation ID',
            'field_changed' => 'Field Changed',
            'old_value' => 'Old Value',
            'new_value' => 'New Value',
            'activity_type' => 'Activity Type',
            'description' => 'Description',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
        ];
    }

    public function getQuotation()
    {
        return $this->hasOne(Quotation::class, ['quotation_id' => 'quotation_id']);
    }

    public function getFormattedCreatedAt($format = 'd-m-Y')
    {
        return Yii::$app->formatter->asDate($this->created_at, 'php:' . $format);
    }

    public function getCreatedBy()
    {
        return $this->hasOne(\app\models\User::class, ['id' => 'created_by']);
    }
}
