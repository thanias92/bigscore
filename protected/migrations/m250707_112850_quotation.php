<?php

use yii\db\Migration;

/**
 * Class m250707_112850_quotation
 */
class m250707_112850_quotation extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Pastikan tabel quotation belum ada sebelum membuat
        if ($this->db->schema->getTableSchema('{{%quotation}}', true) === null) {
            $this->createTable('{{%quotation}}', [
                'quotation_id' => $this->primaryKey(),
                'customer_id' => $this->integer()->notNull(),
                'product_id' => $this->integer()->notNull(),
                'quotation_code' => $this->string()->notNull(),
                'quotation_status' => $this->string()->notNull(),
                'unit_product' => $this->integer()->notNull(),
                'price_product' => $this->integer()->notNull(),
                'total' => $this->string(255)->notNull(),
                'quotation_file' => $this->string(255),
                'created_date' => $this->dateTime(),
                'expiration_date' => $this->dateTime(),
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
                'fk-quotation-customer_id',
                '{{%quotation}}',
                'customer_id',
                '{{%customer}}', // Nama tabel customer Anda
                'customer_id',  // Primary key tabel customer Anda
                'CASCADE',
                'CASCADE'
            );

            // Tambahkan foreign key constraint untuk product_id
            $this->addForeignKey(
                'fk-quotation-product_id',
                '{{%quotation}}',
                'product_id',
                '{{%product}}', // Nama tabel product Anda
                'id_produk',    // Primary key tabel product Anda
                'CASCADE',
                'CASCADE'
            );
        } else {
            echo "Tabel quotation sudah ada, tidak membuat ulang tabel quotation.\n";
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-quotation-product_id', '{{%quotation}}');
        $this->dropForeignKey('fk-quotation-customer_id', '{{%quotation}}');

        if ($this->db->schema->getTableSchema('{{%quotation}}', true) !== null) {
            $this->dropTable('{{%quotation}}');
        } else {
            echo "Tabel quotation tidak ada, tidak menghapus tabel quotation.\n";
        }
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250707_112850_quotation cannot be reverted.\n";

        return false;
    }
    */
}
