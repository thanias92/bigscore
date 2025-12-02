<?php

use yii\db\Migration;

/**
 * Class m250721_065608_add_sales_id_to_contract_table
 */
class m250721_065608_add_sales_id_to_contract_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%contract}}', 'sales_id', $this->integer());

        // Opsional: buat foreign key jika kamu sudah punya tabel user di masa depan
        // $this->addForeignKey(
        //     'fk-contract-sales_id',
        //     '{{%contract}}',
        //     'sales_id',
        //     '{{%user}}',
        //     'id',
        //     'SET NULL',
        //     'CASCADE'
        // );
    }

    public function safeDown()
    {
        // $this->dropForeignKey('fk-contract-sales_id', '{{%contract}}');
        $this->dropColumn('{{%contract}}', 'sales_id');
    }
}
