<?php

use yii\db\Migration;

/**
 * Class m250720_160258_telpvendordanpicTostring
 */
class m250720_160258_telpvendordanpicTostring extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('vendor', 'telp_vendor', $this->string(15));
        $this->alterColumn('vendor', 'telp_PIC', $this->string(15));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250720_160258_telpvendordanpicTostring cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250720_160258_telpvendordanpicTostring cannot be reverted.\n";

        return false;
    }
    */
}
