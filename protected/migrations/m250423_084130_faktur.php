<?php

use yii\db\Migration;

/**
 * Class m250423_083138_faktur
 */
class m250423_084130_faktur extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if ($this->db->schema->getTableSchema('{{%faktur}}', true) === null) {
            $this->createTable('{{%faktur}}', [
                'id_faktur'           => $this->primaryKey(),
                'tanggal_transaksi'   => $this->date()->notNull(),
                'tanggal_jatuh_tempo' => $this->date()->notNull(),
                'no_faktur'           => $this->string()->notNull(),
                'nett'                => $this->string(),
                'keterangan'          => $this->string(),
                'diskon'              => $this->integer(),
                'sisa_tagihan'        => $this->string(),
                'jumlah'              => $this->string(),
                'created_by'          => $this->integer()->notNull(),
                'updated_by'          => $this->integer()->notNull(),
                'deleted_by'          => $this->integer()->notNull(),
                'created_at'          => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
                'updated_at'          => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
                'deleted_at'          => $this->timestamp()->null(),
            ]);
        } else {
            echo "Tabel 'faktur' sudah ada, tidak membuat ulang.\n";
        }
    }

    public function safeDown()
    {
        if ($this->db->schema->getTableSchema('{{%faktur}}', true) !== null) {
            $this->dropTable('{{%faktur}}');
        } else {
            echo "Tabel 'faktur' tidak ditemukan, tidak ada yang dihapus.\n";
        }
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250423_083138_faktur cannot be reverted.\n";

        return false;
    }
    */
}
