<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%customer_history}}`.
 */
class m250807_104133_create_customer_history_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%customer_history}}', [
            'id' => $this->primaryKey(),
            'customer_id' => $this->integer()->notNull(),
            'field_changed' => $this->string(),
            'old_value' => $this->text(),
            'new_value' => $this->text(),
            'activity_type' => $this->string()->notNull(),
            'description' => $this->text()->notNull(),
            'created_by' => $this->integer(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Tambahkan index untuk performa query
        $this->createIndex(
            'idx-customer_history-customer_id',
            '{{%customer_history}}',
            'customer_id'
        );

        // Tambahkan foreign key ke tabel customer
        $this->addForeignKey(
            'fk-customer_history-customer_id',
            '{{%customer_history}}',
            'customer_id',
            '{{%customer}}',
            'customer_id',
            'CASCADE' // Jika customer dihapus, history-nya juga ikut terhapus
        );
    }

    public function safeDown()
    {
        // Hapus foreign key terlebih dahulu
        $this->dropForeignKey(
            'fk-customer_history-customer_id',
            '{{%customer_history}}'
        );

        // Hapus index
        $this->dropIndex(
            'idx-customer_history-customer_id',
            '{{%customer_history}}'
        );

        $this->dropTable('{{%customer_history}}');
    }
}
