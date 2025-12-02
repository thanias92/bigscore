<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%contract_history}}`.
 */
class m250809_023017_create_contract_history_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%contract_history}}', [
            'id' => $this->primaryKey(),
            'contract_id' => $this->integer()->notNull(),
            'activity_type' => $this->string()->notNull(),
            'description' => $this->text()->notNull(),
            'field_changed' => $this->string(),
            'old_value' => $this->text(),
            'new_value' => $this->text(),
            'created_by' => $this->integer(),
            'created_at' => $this->dateTime(),
        ]);

        // Tambahkan indeks untuk performa query
        $this->createIndex(
            'idx-contract_history-contract_id',
            '{{%contract_history}}',
            'contract_id'
        );

        // Tambahkan foreign key ke tabel 'contract'
        $this->addForeignKey(
            'fk-contract_history-contract_id',
            '{{%contract_history}}',
            'contract_id',
            '{{%contract}}', // Nama tabel kontrak
            'contract_id',
            'CASCADE' // Jika kontrak dihapus, historinya juga ikut terhapus
        );

        // Tambahkan indeks untuk created_by
        $this->createIndex(
            'idx-contract_history-created_by',
            '{{%contract_history}}',
            'created_by'
        );

        // Tambahkan foreign key ke tabel 'user'
        $this->addForeignKey(
            'fk-contract_history-created_by',
            '{{%contract_history}}',
            'created_by',
            '{{%user}}', // Asumsi nama tabel user adalah 'user'
            'id',
            'SET NULL' // Jika user dihapus, history tetap ada tapi created_by jadi null
        );
    }

    public function safeDown()
    {
        // Hapus foreign keys terlebih dahulu
        $this->dropForeignKey('fk-contract_history-contract_id', '{{%contract_history}}');
        $this->dropForeignKey('fk-contract_history-created_by', '{{%contract_history}}');

        // Hapus indeks
        $this->dropIndex('idx-contract_history-contract_id', '{{%contract_history}}');
        $this->dropIndex('idx-contract_history-created_by', '{{%contract_history}}');

        // Hapus tabel
        $this->dropTable('{{%contract_history}}');
    }
}
