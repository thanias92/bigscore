<?php

use yii\db\Migration;

/**
 * Class m250630_222003_notification_contract
 */
class m250630_222003_notification_contract extends Migration
{
    /**
     * {@inheritdoc}
     */
      public function safeUp()
    {
        if ($this->db->schema->getTableSchema('{{%notification_contract}}', true) === null) {
            $this->createTable('{{%notification_contract}}', [
                'id_notification_contract' => $this->primaryKey(),
                'contract_id' => $this->integer()->notNull(),
                'status_contract_notification' => $this->string()->notNull(),
                'date_notificatian_contract' => $this->date()->notNull(),
                'description' => $this->text()->null(),
                'created_by' => $this->integer(),
                'updated_by' => $this->integer(),
                'deleted_by' => $this->integer(),
                'created_at' => $this->timestamp(),
                'updated_at' => $this->timestamp(),
                'deleted_at' => $this->timestamp(),
            ]);

            // Foreign Key ke contract
            $this->addForeignKey(
                'fk_notification_contract_contract',
                '{{%notification_contract}}',
                'contract_id',
                '{{%contract}}', // ✅ diperbaiki
                'contract_id',
                'CASCADE',
                'CASCADE'
            );
        } else {
            echo "Tabel notification_contract sudah ada.\n";
        }
    }

    public function safeDown()
    {
        if ($this->db->schema->getTableSchema('{{%notification_contract}}', true) !== null) {
            $this->dropTable('{{%notification_contract}}');
        } else {
            echo "Tabel notification_contract tidak ditemukan.\n";
        }
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250630_222003_notification_contract cannot be reverted.\n";

        return false;
    }
    */
}
