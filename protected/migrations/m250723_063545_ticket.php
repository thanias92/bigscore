<?php

use yii\db\Migration;

/**
 * Class m250723_063545_ticket
 */
class m250723_063545_ticket extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // upadted
        // Periksa apakah tabel verif_resume_medis tidak ada
        if ($this->db->schema->getTableSchema('{{%ticket}}', true) === null) {
            // Jika tidak ada, buat tabel ticket
            $this->createTable('{{%ticket}}', [
            'id_ticket' => $this->primaryKey(),
            'id_deals' => $this->integer(),
            'id_task' => $this->integer(),
            'code_ticket' => $this -> string()->notNull(),
            'user' => $this->string(),
            'role' => $this -> string()-> notNull(),
            'priority_ticket' => $this->string(),
            'label_ticket' => $this->string(),
            'via' => $this ->string()->notNull(),
            'assigne' => $this->string(),
            'modul' => $this->string()->notNull(),
            'title' => $this->string()->notNull(),
            'date_ticket' => $this->dateTime()->notNull(),
            'status_ticket' => $this->string(),
            'description' => $this->text(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
            'deleted_by' => $this->integer(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'deleted_at' => $this->timestamp()->null(),
        ]);
    } else {
        echo "Tabel ticket sudah ada, tidak membuat tabel customer.\n";
    }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Hapus tabel customer hanya jika tabel ticket tidak ada
        if ($this->db->schema->getTableSchema('{{%ticket}}', true) === null) {
            $this->dropTable('{{%ticket}}');
        } else {
            echo "Tabel ticket sudah ada, tidak menghapus tabel ticket.\n";
        }
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250618_085426_ticket cannot be reverted.\n";

        return false;
    }
    */
}
