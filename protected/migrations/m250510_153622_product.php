<?php

use yii\db\Migration;

/**
 * Class m250510_153622_product
 */
class m250510_153622_product extends Migration
{
    /**
     * {@inheritdoc}
     */
    
    public function safeUp()
    {
        $this->createTable('{{%product}}', [
            'id_produk'     => $this->primaryKey(),
            'no_produk'     => $this->string()->notNull(),
            'code_produk'   => $this->string()->notNull(),
            'keterangan'    => $this->string(),
            'unit'          => $this->integer(),
            'product_name'  => $this->string()->notNull(),
            'harga'         => $this->string(), // Gunakan decimal jika ini angka
            'created_by' => $this->integer()->defaultValue(null),
            'updated_by' => $this->integer()->defaultValue(null),
            'deleted_by' => $this->integer()->defaultValue(null),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'deleted_at' => $this->timestamp()->defaultValue(null),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%product}}');
    }
    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250510_153622_product cannot be reverted.\n";

        return false;
    }
    */
}
