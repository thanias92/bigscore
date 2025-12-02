<?php

use yii\db\Migration;

/**
 * Class m250723_144059_alter_tabel_notifpayment
 */
class m250723_144059_alter_tabel_notifpayment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Drop FK lama ke pemasukan_cicilan
        $this->dropForeignKey('fk_notification_payment_cicilan', '{{%notification_payment}}');

        // Rename kolom 'id' menjadi 'id_pemasukan'
        $this->renameColumn('{{%notification_payment}}', 'id', 'id_pemasukan');
        $this->addForeignKey(
            'fk_notification_payment_id_pemasukan',
            '{{%notification_payment}}',
            'id_pemasukan',
            '{{%pemasukan}}',
            'pemasukan_id', // <-- ubah ke nama kolom yang benar
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Rename kembali kolom ke 'id'
        $this->renameColumn('{{%notification_payment}}', 'id_pemasukan', 'id');

        // Tambahkan kembali foreign key ke pemasukan_cicilan
        $this->addForeignKey(
            'fk_notification_payment_cicilan',
            '{{%notification_payment}}',
            'id',
            '{{%pemasukan_cicilan}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250723_144059_alter_tabel_notifpayment cannot be reverted.\n";

        return false;
    }
    */
}
