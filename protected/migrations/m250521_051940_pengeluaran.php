<?php

use yii\db\Migration;

/**
 * Class m250521_051940_pengeluaran
 */
class m250521_051940_pengeluaran extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%pengeluaran}}', [
            'id_pengeluaran'     => $this->primaryKey(),
            'tanggal'            => $this->date()->notNull(),
            'accountkeluar_id' => $this->integer()->notNull(),
            'jumlah'             => $this->integer()->notNull(),
            'jenis_pembayaran'   => $this->string(),
            'no_pengeluaran'    => $this->string(),
            'status_pembayaran'=> $this->string(),
            'bukti_pembayaran'=> $this->string()->null(),
            'id_vendor'             => $this->integer()->notNull(),
            'keterangan'         => $this->string(),
            'created_by' => $this->integer()->defaultValue(null),
            'updated_by' => $this->integer()->defaultValue(null),
            'deleted_by' => $this->integer()->defaultValue(null),
            'created_at'         => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at'         => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'deleted_at'         => $this->timestamp()->defaultValue(null),
        ]);
    }

    public function safeDown()
    {
        if ($this->db->schema->getTableSchema('{{%pengeluaran}}', true) !== null) {
            $this->dropTable('{{%pengeluaran}}');
        } else {
            echo "Tabel 'pengeluaran' tidak ditemukan, tidak ada yang dihapus.\n";
        }
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250521_051940_pengeluaran cannot be reverted.\n";

        return false;
    }
    */
}
