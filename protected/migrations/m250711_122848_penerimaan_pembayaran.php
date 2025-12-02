<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%pemasukan_pembayaran}}`.
 * Has foreign keys to the tables:
 *
 * - `pemasukan`
 * - `accountkeluar`
 */
class m250711_122848_penerimaan_pembayaran extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%penerimaan_pembayaran}}', [
            'id' => $this->primaryKey(),
            'pemasukan_id' => $this->integer()->notNull(),
            'pemasukan_cicilan_id' => $this->integer(),
            'accountkeluar_id' => $this->integer(),
            'jumlah_terbayar' => $this->integer(),
            'bukti_transfer' => $this->string(255),
            'potongan_pajak' => $this->decimal(16, 2)->defaultValue(0),
            'tanggal_bukti_transfer' => $this->date(),
            'deskripsi' => $this->text(),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Add foreign key to pemasukan_cicilan
        $this->addForeignKey(
            'fk_penerimaan_pembayaran_pemasukan_cicilan',
            '{{%penerimaan_pembayaran}}',
            'pemasukan_cicilan_id',
            '{{%pemasukan_cicilan}}',
            'id', // <- ini yang diperbaiki
            'CASCADE'
        );

        // Add foreign key to accountkeluar
        $this->addForeignKey(
            'fk_penerimaan_pembayaran_account',
            '{{%penerimaan_pembayaran}}',
            'accountkeluar_id',
            '{{%accountkeluar}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_penerimaan_pembayaran_pemasukan_cicilan', '{{%penerimaan_pembayaran}}');
        $this->dropForeignKey('fk_penerimaan_pembayaran_account', '{{%penerimaan_pembayaran}}');
        $this->dropTable('{{%penerimaan_pembayaran}}');
    }
}
