<?php

use yii\db\Migration;

/**
 * Class m250526_021726_pemasukan
 */
class m250526_021726_pemasukan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if ($this->db->schema->getTableSchema('{{%pemasukan}}', true) === null) {
            $this->createTable('{{%pemasukan}}', [
                'pemasukan_id' => $this->primaryKey(),
                'deals_id' => $this->integer()->notNull(),
                'accountkeluar_id' => $this->integer()->notNull(),

                // Kolom baru
                
                'pengirim_nama' => $this->string(),
                'pengirim_email' => $this->string(),
                'tipe_pembayaran'=>$this->string(),

                // Keuangan tambahan
                'sub_total' => $this->integer(), // sebelum diskon dan pajak
                'diskon' => $this->integer(),    // potongan harga
                'grand_total' => $this->integer(), // total akhir setelah diskon dan pajak
                'cicilan' => $this->integer(),     // jumlah cicilan jika ada

                // Tetap dipakai
                'purchase_date' => $this->date()->notNull(),
                'description' => $this->string(),
                'no_faktur' => $this->string()->unique(),
                'sisa_tagihan' => $this->integer(),
                'status' => $this->string(255),
                'tgl_jatuhtempo' => $this->date(),
                'bukti_bayar_path' => $this->string(255),

                // Audit Trail
                'created_by' => $this->integer(),
                'updated_by' => $this->integer(),
                'deleted_by' => $this->integer(),
                'created_at' => $this->timestamp(),
                'updated_at' => $this->timestamp(),
                'deleted_at' => $this->timestamp(),
            ]);

            $this->addForeignKey(
                'fk-pemasukan-deals_id',
                '{{%pemasukan}}',
                'deals_id',
                '{{%deals}}',
                'deals_id',
                'CASCADE',
                'CASCADE'
            );
        } else {
            echo "Tabel pemasukan sudah ada, tidak membuat ulang tabel pemasukan.\n";
        }
    }
    //testa

    public function safeDown()
    {
        if ($this->db->schema->getTableSchema('{{%pemasukan}}', true) !== null) {
            $this->dropTable('{{%pemasukan}}');
        } else {
            echo "Tabel 'pemasukan' tidak ditemukan, tidak ada yang dihapus.\n";
        }
    }
}