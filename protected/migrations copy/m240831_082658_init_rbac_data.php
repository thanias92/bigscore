<?php

use yii\db\Migration;
use mdm\admin\models\Assignment;

/**
 * Class m240831_082658_init_rbac_data
 */
class m240831_082658_init_rbac_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $user = \app\models\User::find()->where(['username' => 'admin'])->one();
      $id = $user->id;
      $items = ['Administrator'];
      $model = new Assignment($id);
      $success = $model->assign($items);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240831_082658_init_rbac_data cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240831_082658_init_rbac_data cannot be reverted.\n";

        return false;
    }
    */
}
