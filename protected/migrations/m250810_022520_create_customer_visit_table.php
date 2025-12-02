<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%customer_visit}}`.
 */
class m250810_022520_create_customer_visit_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%customer_visit}}', [
            'id' => $this->primaryKey(),
            'customer_id' => $this->integer()->notNull(),
            'visit_date' => $this->date()->notNull(),
            'notes' => $this->text(),
            'created_by' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_by' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);

        // --- TAMBAHAN: Buat Index ---
        $this->createIndex(
            'idx-customer_visit-customer_id',
            '{{%customer_visit}}',
            'customer_id'
        );

        $this->createIndex(
            'idx-customer_visit-created_by',
            '{{%customer_visit}}',
            'created_by'
        );

        // Tambahkan foreign key ke tabel customer
        $this->addForeignKey(
            'fk-customer_visit-customer_id',
            '{{%customer_visit}}',
            'customer_id',
            '{{%customer}}',
            'customer_id',
            'CASCADE'
        );

        // Tambahkan foreign key ke tabel user (salesman)
        $this->addForeignKey(
            'fk-customer_visit-created_by',
            '{{%customer_visit}}',
            'created_by',
            '{{%user}}',
            'id',
            'SET NULL'
        );
    }

    public function safeDown()
    {
        // Drop foreign keys
        $this->dropForeignKey('fk-customer_visit-customer_id', '{{%customer_visit}}');
        $this->dropForeignKey('fk-customer_visit-created_by', '{{%customer_visit}}');

        // --- TAMBAHAN: Drop Index ---
        $this->dropIndex('idx-customer_visit-customer_id', '{{%customer_visit}}');
        $this->dropIndex('idx-customer_visit-created_by', '{{%customer_visit}}');

        // Drop table
        $this->dropTable('{{%customer_visit}}');
    }
}
