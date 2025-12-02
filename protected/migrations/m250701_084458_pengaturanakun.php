<?php

use yii\db\Migration;

/**
 * Class m250701_084458_pengaturanakun
 */
class m250701_084458_pengaturanakun extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%pengaturanakun}}', [
            'pengaturanakun_id' => $this->primaryKey(),
            'pemasukan_id' => $this->integer()->notNull(),
            'logo' => $this->string()->null(),
            'ttd' => $this->string()->null(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'deleted_at' => $this->timestamp()->null(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
            'deleted_by' => $this->integer(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%pengaturanakun}}');
    }


    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250701_084458_pengaturanakun cannot be reverted.\n";

        return false;
    }
    */
}
