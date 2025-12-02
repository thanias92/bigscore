<?php

use yii\db\Migration;

/**
 * Class m250502_034246_task
 */
//commit
class m250502_034246_task extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if ($this->db->schema->getTableSchema('{{%task}}', true) === null) {
            $this->createTable('{{%task}}', [
                'id_task' => $this->primaryKey(),
                'title' => $this->string()->notNull(),
                'label_task' => $this->string(),
                'modul' => $this->string(),
                'priority_task' => $this->string(),
                'assign' => $this->string(),
                'status' => $this->string(),
                'duedate_task' => $this->dateTime(),
                'finishdate_task' => $this->dateTime(),
                'description' => $this->text(),
                'id_ticket' => $this->integer()->defaultValue(null),
                'customer_id' => $this->integer()->defaultValue(null),

                // Tambahan audit trail
                'created_by' => $this->integer()->defaultValue(null),
                'updated_by' => $this->integer()->defaultValue(null),
                'deleted_by' => $this->integer()->defaultValue(null),
                'created_at' => $this->timestamp()->defaultValue(null),
                'updated_at' => $this->timestamp()->defaultValue(null),
                'deleted_at' => $this->timestamp()->defaultValue(null),

            ]);
        } else {
            echo "Tabel sub_task sudah ada, tidak membuat tabel sub_task.\n";
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if ($this->db->schema->getTableSchema('{{%task}}', true) === null) {
            $this->dropTable('{{%task}}');
        } else {
            echo "Tabel task tidak ada, tidak menghapus tabel customer.\n";
        }
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250502_034246_task cannot be reverted.\n";

        return false;
    }
    */
}
