<?php

use yii\db\Migration;

/**
 * Class m240830_134313_init_dummy_account
 */
class m240830_134313_init_dummy_account extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $admin = new \app\models\User();
      $admin->username = 'admin';
      $admin->email = 'admin@gmail.com';
      $admin->status = $admin::STATUS_ACTIVE;
      $admin->setPassword('admin');
      $admin->generateAuthKey();
      $admin->save(false);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240830_134313_init_dummy_account cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240830_134313_init_dummy_account cannot be reverted.\n";

        return false;
    }
    */
}
