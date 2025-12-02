<?php

use yii\db\Migration;

/**
 * Class m250617_113649_log_email_notification_payment
 */
class m250617_113649_log_email_notification_payment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if ($this->db->schema->getTableSchema('{{%log_email_notification_payment}}', true) === null) {
            $this->createTable('{{%log_email_notification_payment}}', [
                'id_log' => $this->primaryKey(),
                'customer_id' => $this->integer()->notNull(),
                'status' => $this->string()->notNull(),
                'sent_at' => $this->dateTime()->notNull(),
                'subject' => $this->string()->null(),
                'message' => $this->text()->null(),
                'created_by' => $this->integer()->null(),
                'updated_by' => $this->integer()->null(),
                'deleted_by' => $this->integer()->null(),
                'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
                'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
                'deleted_at' => $this->timestamp()->null(),
            ]);

            // Tambahkan foreign key ke tabel customer
            $this->addForeignKey(
                'fk_log_email_notification_payment_customer',
                '{{%log_email_notification_payment}}',
                'customer_id',
                '{{%customer}}', // ganti jika nama tabel customer kamu beda
                'customer_id',
                'CASCADE',
                'CASCADE'
            );
        } else {
            echo "Tabel log_email_notification_payment sudah ada.\n";
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if ($this->db->schema->getTableSchema('{{%log_email_notification_payment}}', true) !== null) {
            $this->dropTable('{{%log_email_notification_payment}}');
        } else {
            echo "Tabel log_email_notification_payment tidak ditemukan.\n";
        }
    }/*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250617_113649_log_email_notification_payment cannot be reverted.\n";

        return false;
    }
    */
}
