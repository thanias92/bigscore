<?php

use yii\db\Migration;

/**
 * Class m250430_040630_customer
 */
class m250430_040630_customer extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if ($this->db->schema->getTableSchema('{{%customer}}', true) === null) {
            // Jika tidak ada, buat tabel customer
            $this->createTable('{{%customer}}', [
                'customer_id' => $this->primaryKey(),
                'customer_name' => $this->string(255)->notNull(),
                'customer_email' => $this->string(255)->notNull(),
                'customer_phone' => $this->string(255)->notNull(),
                'customer_address' => $this->string(255)->notNull(),
                'customer_website' => $this->string(255)->notNull(),
                'establishment_date' => $this->dateTime()->notNull(),
                'customer_source' => $this->string(255)->notNull(),
                'pic_name' => $this->string(255)->notNull(),
                'pic_email' => $this->string(255)->notNull(),
                'pic_phone' => $this->string(255)->notNull(),
                'pic_workroles' => $this->string(255)->notNull(),
                'created_by' => $this->integer(),
                'updated_by' => $this->integer(),
                'deleted_by' => $this->integer(),
                'created_at' => $this->timestamp(),
                'updated_at' => $this->timestamp(),
                'deleted_at' => $this->timestamp()
            ]);
        } else {
            echo "Tabel customer sudah ada, tidak membuat tabel customer.\n";
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if ($this->db->schema->getTableSchema('{{%customer}}', true) !== null) {
            $this->dropTable('{{%customer}}');
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
        echo "m250430_040630_customer cannot be reverted.\n";

        return false;
    }
    */

    //test migrate
}
