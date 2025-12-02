<?php

use yii\db\Migration;

/**
 * Class m240830_143524_create_tenant
 */
class m240830_143524_create_tenant extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('tenant', [
            'id' => $this->bigPrimaryKey(),
            'uuid' => $this->string(128)->notNull(),
            'code' => $this->string(20)->notNull(),
            'name' => $this->string(128)->notNull(),
            'email' => $this->string(128)->notNull(),
            'phone' => $this->string(128)->notNull(),
            'address' => $this->text()->notNull(),
            'host' => $this->string()->notNull(),
            'status' => $this->boolean()->defaultValue(true),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'deleted_at' => $this->integer(),
            'deleted_by' => $this->integer(),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240830_143524_create_tenant cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240830_143524_create_tenant cannot be reverted.\n";

        return false;
    }
    */
}
