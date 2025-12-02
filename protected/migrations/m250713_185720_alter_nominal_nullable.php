<?php

use yii\db\Migration;

/**
 * Class m250713_185720_alter_nominal_nullable
 */
class m250713_185720_alter_nominal_nullable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('pemasukan_cicilan', 'nominal', $this->integer()->null());
    }

    public function safeDown()
    {
        // Jika Anda ingin rollback ke NOT NULL, pastikan tidak ada nilai NULL terlebih dahulu
        // atau tentukan default seperti 1
        $this->alterColumn('pemasukan_cicilan', 'nominal', $this->integer()->notNull());
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250713_185720_alter_nominal_nullable cannot be reverted.\n";

        return false;
    }
    */
}
