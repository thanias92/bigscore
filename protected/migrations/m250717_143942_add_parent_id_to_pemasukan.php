<?php

use yii\db\Migration;

/**
 * Class m250717_143942_add_parent_id_to_pemasukan
 */
class m250717_143942_add_parent_id_to_pemasukan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('pemasukan', 'parent_id', $this->integer()->null());
        $this->addForeignKey('fk_pemasukan_parent', 'pemasukan', 'parent_id', 'pemasukan', 'pemasukan_id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Hapus foreign key terlebih dahulu
        $this->dropForeignKey('fk_pemasukan_parent', 'pemasukan');

        // Hapus kolom parent_id
        $this->dropColumn('pemasukan', 'parent_id');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250717_143942_add_parent_id_to_pemasukan cannot be reverted.\n";

        return false;
    }
    */
}
