<?php

use yii\db\Migration;

/**
 * Class m250625_151139_contract
 */
class m250625_151139_contract extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Pastikan tabel contract belum ada sebelum membuat
        if ($this->db->schema->getTableSchema('{{%contract}}', true) === null) {
            $this->createTable('{{%contract}}', [
                'contract_id' => $this->primaryKey(),
                'contract_code' => $this->string()->notNull(),
                'invoice_id' => $this->integer()->notNull(),
                'start_date' => $this->dateTime(),
                'end_date' => $this->dateTime(),
                'evidence_contract' => $this->string()->notNull(),
                'status_contract' => $this->string()->notNull(),
                'description' => $this->text(),

                // Audit Trail Columns
                'created_by' => $this->integer(),
                'updated_by' => $this->integer(),
                'deleted_by' => $this->integer(),
                'created_at' => $this->timestamp(),
                'updated_at' => $this->timestamp(),
                'deleted_at' => $this->timestamp(),
            ]);

            // Tambahkan foreign key constraint untuk invoice_id
            $this->addForeignKey(
                'fk-contract-invoice_id',
                '{{%contract}}',
                'invoice_id',
                '{{%pemasukan}}', // Nama tabel pemasukan Anda
                'pemasukan_id',  // Primary key tabel pemasukan Anda
                'CASCADE',
                'CASCADE'
            );

            // Tambahkan foreign key untuk created_by, updated_by, deleted_by (jika Anda punya tabel user)
            // Asumsi nama tabel user Anda adalah 'user' dan primary key-nya 'id'
            /*
            $this->addForeignKey(
                'fk-deals-created_by',
                '{{%deals}}',
                'created_by',
                '{{%user}}', // Ganti dengan nama tabel user Anda
                'id',        // Ganti dengan primary key tabel user Anda
                'SET NULL',  // Atau 'RESTRICT' jika tidak boleh hapus user
                'CASCADE'
            );
            $this->addForeignKey(
                'fk-deals-updated_by',
                '{{%deals}}',
                'updated_by',
                '{{%user}}', // Ganti dengan nama tabel user Anda
                'id',        // Ganti dengan primary key tabel user Anda
                'SET NULL',
                'CASCADE'
            );
            $this->addForeignKey(
                'fk-deals-deleted_by',
                '{{%deals}}',
                'deleted_by',
                '{{%user}}', // Ganti dengan nama tabel user Anda
                'id',        // Ganti dengan primary key tabel user Anda
                'SET NULL',
                'CASCADE'
            );
            */
        } else {
            echo "Tabel contract sudah ada, tidak membuat ulang tabel contract.\n";
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Hapus foreign key constraints saat rollback (urutan harus terbalik dari safeUp)
        // Pertama, hapus foreign key ke user (jika ditambahkan)
        /*
        $this->dropForeignKey('fk-deals-deleted_by', '{{%deals}}');
        $this->dropForeignKey('fk-deals-updated_by', '{{%deals}}');
        $this->dropForeignKey('fk-deals-created_by', '{{%deals}}');
        */

        // Hapus tabel contract jika ada
        if ($this->db->schema->getTableSchema('{{%contract}}', true) !== null) {
            $this->dropTable('{{%contract}}');
        } else {
            echo "Tabel contract tidak ada, tidak menghapus tabel contract.\n";
        }

        /*
        // Use up()/down() to run migration code without a transaction.
        public function up()
        {

        }

        public function down()
        {
            echo "m250625_151139_contract cannot be reverted.\n";

            return false;
        }
        */
    }
}
