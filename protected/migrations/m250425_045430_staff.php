<?php

use yii\db\Migration;

/**
 * Class m250425_045430_staff
 */
class m250425_045430_staff extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if ($this->db->schema->getTableSchema('{{%staff}}', true) === null) {
            // Jika tidak ada, buat tabel staff
            $this->createTable('{{%staff}}', [
                'id_staff' => $this->primaryKey(),
                'username' => $this->string()->notNull(),
                'password' => $this->string()->notNull(),
                'nama_lengkap' => $this->string()->notNull(),
                'jenis_identitas' => $this->string()->notNull(),
                'no_identitas' => $this->string()->notNull(),
                'jenis_kelamin' => $this->string()->notNull(),
                'status_perkawinan' => $this->string()->notNull(),
                'tanggal_lahir' => $this->dateTime()->notNull(),
                'tempat_lahir' => $this->string()->notNull(),
                'nama_ibu_kandung' => $this->string()->notNull(),
                'id_agama' => $this->integer(),
                'suku' => $this->string(),
                'id_pendidikan' => $this->integer(),
                'id_pekerjaan' => $this->integer(),
                'pekerjaan_lain' => $this->string(),
                'no_hp' => $this->string(),
                'no_wa' => $this->string(),
                'no_hp_alternatif' => $this->string(),
                'alamat' => $this->string(),
                'rt' => $this->string(5),
                'rw' => $this->string(5),
                'id_negara' => $this->integer(),
                'id_provinsi' => $this->integer(),
                'id_kabupaten' => $this->integer(),
                'id_kecamatan' => $this->integer(),
                'id_kelurahan_desa' => $this->integer(),
                'no_pegawai' => $this->string(),
                'jenis_pegawai' => $this->string(),
                'created_by' => $this->integer(),
                'updated_by' => $this->integer(),
                'deleted_by' => $this->integer(),
                'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
                'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
                'deleted_at' => $this->timestamp()->null(),
            ]);
        } else {
            echo "Tabel staff sudah ada, tidak membuat tabel staff.\n";
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Hapus tabel staff hanya jika tabel staff tidak ada
        if ($this->db->schema->getTableSchema('{{%staff}}', true) === null) {
            $this->dropTable('{{%staff}}');
        } else {
            echo "Tabel staff tidak ada, tidak menghapus tabel staff.\n";
        }
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250425_044924_staff cannot be reverted.\n";

        return false;
    }
    */
}
