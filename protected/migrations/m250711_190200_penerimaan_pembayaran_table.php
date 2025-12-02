<?php

use yii\db\Migration;

/**
 * Class m250711_190200_penerimaan_pembayaran_table
 */
class m250711_190200_penerimaan_pembayaran_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%penerimaan_pembayaran}}', 'metode_pembayaran', $this->string(255)->after('tanggal_pembayaran'));
        $this->addColumn('{{%penerimaan_pembayaran}}', 'referensi_pembayaran', $this->string(255)->after('metode_pembayaran'));
        $this->addColumn('{{%penerimaan_pembayaran}}', 'catatan', $this->text()->after('referensi_pembayaran'));
        $this->addColumn('{{%penerimaan_pembayaran}}', 'jumlah_pembayaran', $this->decimal(15, 2)->after('bukti_pembayaran')); // Use decimal for currency
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%penerimaan_pembayaran}}', 'jumlah_pembayaran');
        $this->dropColumn('{{%penerimaan_pembayaran}}', 'catatan');
        $this->dropColumn('{{%penerimaan_pembayaran}}', 'referensi_pembayaran');
        $this->dropColumn('{{%penerimaan_pembayaran}}', 'metode_pembayaran');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250711_190200_penerimaan_pembayaran_table cannot be reverted.\n";

        return false;
    }
    */
}
