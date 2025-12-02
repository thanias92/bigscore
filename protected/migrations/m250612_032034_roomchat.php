<?php

use yii\db\Migration;

/**
 * Class m250612_032034_roomchat 
 */
class m250612_032034_roomchat extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Periksa apakah tabel roomchat tidak ada
        if ($this->db->schema->getTableSchema('{{%roomchat}}', true) === null) {
            // Jika tidak ada, buat tabel roomchat
            $this->createTable('{{%roomchat}}', [
                'id_chat' => $this->primaryKey(),
                'id_customer' => $this->integer(),
                'id_staff' => $this->integer(),
                'chat' => $this->string()->notNull(),
                'send_at' => $this->dateTime()->notNull(),
                'is_read' => $this->boolean()->defaultValue(false),
                'created_by' => $this->integer(),
                'updated_by' => $this->integer(),
                'deleted_by' => $this->integer(),
                'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
                'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
                'deleted_at' => $this->timestamp()->null(),
            ]);
        } else {
            echo "Tabel roomchat sudah ada, tidak membuat tabel roomchat.\n";
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Hapus tabel roomchat hanya jika tabel roomchat tidak ada
        if ($this->db->schema->getTableSchema('{{%roomchat}}', true) === null) {
            $this->dropTable('{{%roomchat}}');
        } else {
            echo "Tabel roomchat tidak ada, tidak menghapus tabel roomchat.\n";
        }
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250612_032034_roomchat  cannot be reverted.\n";

        return false;
    }
    */
}
