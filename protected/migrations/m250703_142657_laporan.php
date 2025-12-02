<?php

use yii\db\Migration;

/**
 * Class m250703_142657_laporan
 */
class m250703_142657_laporan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if ($this->db->schema->getTableSchema('{{%laporan}}', true) === null) {
            $this->createTable('{{%laporan}}', [
                'laporan_id'       => $this->primaryKey(),
                'pemasukan_id'     => $this->integer()->notNull(),
                'pengeluaran_id'   => $this->integer()->notNull(),

                'tanggal'          => $this->date()->notNull(),
                'tipe_laporan'     => $this->string(50)->notNull(), // contoh: keuangan, laba_rugi, arus_kas
                'jumlah_pemasukan' => $this->integer(),
                'jumlah_pengeluaran' => $this->integer(),
                'saldo_akhir'      => $this->integer(),

                'created_by' => $this->integer(),
                'updated_by' => $this->integer(),
                'deleted_by' => $this->integer(),
                'created_at' => $this->timestamp(),
                'updated_at' => $this->timestamp(),
                'deleted_at' => $this->timestamp(),
            ]);

            // Foreign key ke pemasukan
            $this->addForeignKey(
                'fk-laporan-pemasukan_id',
                '{{%laporan}}',
                'pemasukan_id',
                '{{%pemasukan}}',
                'pemasukan_id',
                'CASCADE',
                'CASCADE'
            );

            // Foreign key ke pengeluaran
            $this->addForeignKey(
                'fk-laporan-pengeluaran_id',
                '{{%laporan}}',
                'pengeluaran_id',
                '{{%pengeluaran}}',
                'id_pengeluaran',
                'CASCADE',
                'CASCADE'
            );
        } else {
            echo "Tabel laporan sudah ada.\n";
        }
    }

    public function safeDown()
    {
        if ($this->db->schema->getTableSchema('{{%laporan}}', true) !== null) {
            $this->dropTable('{{%laporan}}');
        } else {
            echo "Tabel 'laporan' tidak ditemukan, tidak ada yang dihapus.\n";
        }
    }
}
