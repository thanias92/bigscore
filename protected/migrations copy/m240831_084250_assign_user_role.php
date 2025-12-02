<?php

use yii\db\Migration;

/**
 * Class m240831_084250_assign_user_role
 */
class m240831_084250_assign_user_role extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240831_084250_assign_user_role cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240831_084250_assign_user_role cannot be reverted.\n";

        return false;
    }
    */
}
