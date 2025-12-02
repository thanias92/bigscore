<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "deals_history".
 *
 * @property int $id
 * @property int $deals_id
 * @property string|null $old_label
 * @property string|null $new_label
 * @property string|null $field_changed
 * @property string|null $old_value
 * @property string|null $new_value
 * @property string $activity_type
 * @property string $description
 * @property int|null $created_by
 * @property string|null $created_at
 *
 * @property Deals $deals
 * @property User $createdBy
 */
class DealsHistory extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'deals_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['deals_id', 'activity_type', 'description'], 'required'],
            [['deals_id', 'created_by'], 'integer'],
            [['old_value', 'new_value', 'description'], 'string'],
            [['created_at'], 'safe'],
            [['old_label', 'new_label', 'field_changed', 'activity_type'], 'string', 'max' => 255],
            [['deals_id'], 'exist', 'skipOnError' => true, 'targetClass' => Deals::class, 'targetAttribute' => ['deals_id' => 'deals_id']],

            // PERUBAHAN #1: Aktifkan kembali validasi relasi ke User
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
            'deals_id' => 'Deals ID',
            'old_label' => 'Old Label',
            'new_label' => 'New Label',
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
     * Gets query for [[Deals]].
     * @return \yii\db\ActiveQuery
     */
    public function getDeals()
    {
        return $this->hasOne(Deals::class, ['deals_id' => 'deals_id']);
    }

    /**
     * PERUBAHAN #2: Aktifkan kembali relasi ke User.
     * Gets query for [[CreatedBy]].
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    // Helper function to format created_at for display (tetap sama)
    public function getFormattedCreatedAt($format = 'd-m-Y')
    {
        return Yii::$app->formatter->asDate($this->created_at, 'php:' . $format);
    }

    /**
     * PERUBAHAN #3: Perbaiki helper ini agar menampilkan username.
     * Helper function to get the user name.
     */
    public function getCreatedByUserName()
    {
        // Cek apakah relasi createdBy ada dan tidak null.
        // Jika ada, tampilkan username. Jika tidak, tampilkan "System".
        if ($this->createdBy) {
            return $this->createdBy->username;
        }
        return 'System';
    }
}
