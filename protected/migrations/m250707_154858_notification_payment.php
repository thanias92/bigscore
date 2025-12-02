<?php

use yii\db\Migration;

/**
 * Class m250707_154858_notification_payment 
 */
class m250707_154858_notification_payment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if ($this->db->schema->getTableSchema('{{%notification_payment}}', true) === null) {
            $this->createTable('{{%notification_payment}}', [
                'id_notification_payment' => $this->primaryKey(),
                'id' => $this->integer()->notNull(), //ini id nya si pemasukan_cicilan
                'status_payment_notification' => $this->string()->notNull(),
                'date_notificatian' => $this->date()->notNull(),
                'created_by' => $this->integer(),
                'updated_by' => $this->integer(),
                'deleted_by' => $this->integer(),
                'created_at' => $this->timestamp(),
                'updated_at' => $this->timestamp(),
                'deleted_at' => $this->timestamp(),
            ]);

            // Tambahkan foreign key ke tabel pemasukan_cicilan
            $this->addForeignKey(
                'fk_notification_payment_cicilan',
                '{{%notification_payment}}',
                'id',
                '{{%pemasukan_cicilan}}',
                'id',
                'CASCADE',
                'CASCADE'
            );
        } else {
            echo "Tabel notification_payment sudah ada.\n";
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if ($this->db->schema->getTableSchema('{{%notification_payment}}', true) !== null) {
            $this->dropForeignKey('fk_notification_payment_cicilan', '{{%notification_payment}}');
            $this->dropTable('{{%notification_payment}}');
        } else {
            echo "Tabel notification_payment tidak ditemukan.\n";
        }
    }
}
