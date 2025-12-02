<?php

use yii\db\Migration;

/**
 * Class m250702_080933_deals
 */
class m250702_080933_deals extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Pastikan tabel deals belum ada sebelum membuat
        if ($this->db->schema->getTableSchema('{{%deals}}', true) === null) {
            $this->createTable('{{%deals}}', [
                'deals_id' => $this->primaryKey(),
                'customer_id' => $this->integer()->notNull(),
                'product_id' => $this->integer()->notNull(),
                'deals_code' => $this->string()->notNull(),
                'label_deals' => $this->string()->notNull(),
                'unit_product' => $this->integer()->notNull(),
                'price_product' => $this->integer()->notNull(),
                'total' => $this->string(255)->notNull(),
                'purchase_type' => $this->string(),
                'purchase_date' => $this->dateTime(),
                'description' => $this->text()->notNull(),
                'created_by' => $this->integer(),
                'updated_by' => $this->integer(),
                'deleted_by' => $this->integer(),
                'created_at' => $this->timestamp(),
                'updated_at' => $this->timestamp(),
                'deleted_at' => $this->timestamp(),
            ]);

            // Tambahkan foreign key constraint untuk customer_id
            $this->addForeignKey(
                'fk-deals-customer_id',
                '{{%deals}}',
                'customer_id',
                '{{%customer}}', // Nama tabel customer Anda
                'customer_id',  // Primary key tabel customer Anda
                'CASCADE',
                'CASCADE'
            );

            // Tambahkan foreign key constraint untuk product_id
            $this->addForeignKey(
                'fk-deals-product_id',
                '{{%deals}}',
                'product_id',
                '{{%product}}', // Nama tabel product Anda
                'id_produk',    // Primary key tabel product Anda
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
            echo "Tabel deals sudah ada, tidak membuat ulang tabel deals.\n";
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

        $this->dropForeignKey('fk-deals-product_id', '{{%deals}}');
        $this->dropForeignKey('fk-deals-customer_id', '{{%deals}}');

        if ($this->db->schema->getTableSchema('{{%deals}}', true) !== null) {
            $this->dropTable('{{%deals}}');
        } else {
            echo "Tabel deals tidak ada, tidak menghapus tabel deals.\n";
        }
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250702_080933_deals cannot be reverted.\n";

        return false;
    }
    */
}
