<?php

use yii\db\Migration;

/**
 * Class m250713_142235_alter_accountkeluar_nullable
 */
class m250713_142235_alter_accountkeluar_nullable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('pemasukan', 'accountkeluar_id', $this->integer()->null());

    }

    public function safeDown()
    {
        // Jika Anda ingin rollback ke NOT NULL, pastikan tidak ada nilai NULL terlebih dahulu
        // atau tentukan default seperti 1
        $this->alterColumn('pemasukan', 'accountkeluar_id', $this->integer()->notNull());

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250713_142235_alter_accountkeluar_nullable cannot be reverted.\n";

        return false;
    }
    */
}
