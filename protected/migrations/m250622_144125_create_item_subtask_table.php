<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%item_subtask}}`.
 */
class m250622_144125_create_item_subtask_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('item_subtask', [
            'id_item_subtask' => $this->primaryKey(),
            'id_subtask' => $this->integer()->notNull(),
            'item_subtask' => $this->string()->notNull(),
            'status' => $this->boolean()->defaultValue(false),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
            'deleted_by' => $this->integer(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->null(),
            'deleted_at' => $this->timestamp()->null(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('item_subtask');
    }
}
