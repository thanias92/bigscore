<?php

use yii\db\Migration;

/**
 * Class m250430_041245_sub_task
 */
class m250430_041245_sub_task extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Periksa apakah tabel verif_resume_medis tidak ada
        if ($this->db->schema->getTableSchema('{{%sub_task}}', true) === null) {
            // Jika tidak ada, buat tabel customer
            $this->createTable('{{%sub_task}}', [
                'id_subtask' => $this->primaryKey(),
                'title_subtask' => $this->string()->notNull(),
                'item_subtask' => $this->string()->defaultValue(null),
                'progress_subtask' => $this->string()->defaultValue(null),
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

    public function safeDown()
    {
        // Hapus tabel customer hanya jika tabel ticket tidak ada
        if ($this->db->schema->getTableSchema('{{%sub_task}}', true) === null) {
            $this->dropTable('{{%sub_task}}');
        } else {
            echo "Tabel customer tidak ada, tidak menghapus tabel customer.\n";
        }
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250430_041245_sub_task cannot be reverted.\n";

        return false;
    }
    */
}
