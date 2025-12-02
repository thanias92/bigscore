<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "item_subtask".
 *
 * @property int $id_item_subtask
 * @property int $id_subtask
 * @property string $item_subtask
 * @property bool $status
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 */
class ItemSubtask extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'item_subtask';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_subtask', 'item_subtask'], 'required'],
            [['id_subtask', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['status'], 'boolean'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['item_subtask'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_item_subtask' => 'ID Item Subtask',
            'id_subtask' => 'ID Subtask',
            'item_subtask' => 'Item Subtask',
            'status' => 'Status',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'deleted_by' => 'Deleted By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted_at' => 'Deleted At',
        ];
    }

    /**
     * Gets related SubTask.
     */
    public function getSubtask()
    {
        return $this->hasOne(SubTask::class, ['id_subtask' => 'id_subtask']);
    }

    /**
     * Automatically handle timestamps
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
        ];
    }
}
