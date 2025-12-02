<?php

use yii\db\Migration;

/**
 * Class m250510_134303_implementation
 */
//commit
class m250510_134303_implementation extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('implementation', [
            'id_implementasi' => $this->primaryKey(), // Primary Key
            'activity_title' => $this->string()->notNull(),
            'activity' => $this->string()->notNull(),
            'detail' => $this->string()->notNull(),
            'start_date' => $this->dateTime()->notNull(),
            'completion_date' => $this->dateTime()->null(),
            'pic_aktivitas' => $this->string()->notNull(),
            'status' => $this->string()->notNull(),
            'deals_id' => $this->integer()->notNull(),
            'notes' => $this->text()->null(),
            'duration' => $this->time(), // Assuming you want a time column for duration

            // Progress
            'line_progress' => $this->integer()->defaultValue(0),

            // Audit Trail
            'created_by' => $this->integer()->defaultValue(null),
            'updated_by' => $this->integer()->defaultValue(null),
            'deleted_by' => $this->integer()->defaultValue(null),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'deleted_at' => $this->timestamp()->defaultValue(null),
        ]);

        // Optional: Add index or foreign keys if needed, for example:
        // $this->createIndex('idx-task-created_by', 'task', 'created_by');
    }
    public function safeDown()
    {
        $this->dropTable('{{%implementation}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250510_134303_implementation cannot be reverted.\n";

        return false;
    }
    */
}
