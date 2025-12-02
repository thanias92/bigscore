<?php

use yii\db\Migration;

/**
 * Class m250620_065319_feedback2
 */
class m250620_065319_feedback2 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Periksa apakah tabel feedback tidak ada
        if ($this->db->schema->getTableSchema('{{%feedback}}', true) === null) {
            // Jika tidak ada, buat tabel feedback
            $this->createTable('{{%feedback}}', [
                'id_feedback' => $this->primaryKey(),
                'id_deals' => $this->integer(),
                'date_feedback' => $this->dateTime()->notNull(),
                'feedback' => $this->string()->notNull(),
                'rate' => $this->string()->notNull(),
                'created_by' => $this->integer(),
                'updated_by' => $this->integer(),
                'deleted_by' => $this->integer(),
                'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
                'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
                'deleted_at' => $this->timestamp()->null(),
            ]);
        } else {
            echo "Tabel feedback sudah ada, tidak membuat tabel feedback.\n";
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Hapus tabel feedback hanya jika tabel feedback tidak ada
        if ($this->db->schema->getTableSchema('{{%feedback}}', true) === null) {
            $this->dropTable('{{%feedback}}');
        } else {
            echo "Tabel feedback tidak ada, tidak menghapus tabel feedback.\n";
        }
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250620_065319_feedback2 cannot be reverted.\n";

        return false;
    }
    */
}
