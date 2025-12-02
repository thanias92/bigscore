<?php

use yii\db\Migration;

/**
 * Class m250725_210517_add_optional_product_fields_to_quotation_table
 */
class m250725_210517_add_optional_product_fields_to_quotation_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%quotation}}', 'optional_product_id', $this->integer());
        $this->addColumn('{{%quotation}}', 'optional_unit_product', $this->integer()->defaultValue(1));
        $this->addColumn('{{%quotation}}', 'optional_price_product', $this->decimal(19, 2)->defaultValue(0));
        $this->addColumn('{{%quotation}}', 'optional_total', $this->decimal(19, 2)->defaultValue(0));

        // Menambahkan foreign key (opsional tapi direkomendasikan)
        $this->addForeignKey(
            'fk-quotation-optional_product_id',
            '{{%quotation}}',
            'optional_product_id',
            '{{%product}}', // Asumsi nama tabel produk adalah 'product'
            'id_produk',    // Asumsi primary key di tabel produk adalah 'id_produk'
            'SET NULL',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-quotation-optional_product_id', '{{%quotation}}');
        $this->dropColumn('{{%quotation}}', 'optional_total');
        $this->dropColumn('{{%quotation}}', 'optional_price_product');
        $this->dropColumn('{{%quotation}}', 'optional_unit_product');
        $this->dropColumn('{{%quotation}}', 'optional_product_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250725_210517_add_optional_product_fields_to_quotation_table cannot be reverted.\n";

        return false;
    }
    */
}
