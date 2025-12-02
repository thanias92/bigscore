<?php

use yii\db\Migration;

/**
 * Class m250424_154845_vendor
 */
class m250502_033035_vendor extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if ($this->db->schema->getTableSchema('{{%vendor}}', true) === null) {
            $this->createTable('{{%vendor}}', [
                'id_vendor'     => $this->primaryKey(),
                'nama_vendor'   => $this->string()->notNull(),
                'alamat_vendor' => $this->string(),
                'email_vendor'   => $this->string(), // jika typo, ubah jadi `email_vendor`
                'telp_vendor'   => $this->integer(),
                'nama_PIC'      => $this->string(),
                'email_PIC'     => $this->string(),
                'telp_PIC'      => $this->integer(),
                'created_by'    => $this->integer()->notNull(),
                'updated_by'    => $this->integer()->notNull(),
                'deleted_by'    => $this->integer()->notNull(),
                'created_at'    => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
                'updated_at'    => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP '),
                'deleted_at'    => $this->timestamp()->null(),
            ]);
        } else {
            echo "Tabel 'vendor' sudah ada, tidak membuat ulang.\n";
        }
    }

    public function safeDown()
    {
        if ($this->db->schema->getTableSchema('{{%vendor}}', true) !== null) {
            $this->dropTable('{{%vendor}}');
        } else {
            echo "Tabel 'vendor' tidak ditemukan, tidak ada yang dihapus.\n";
        }
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250424_154845_vendor cannot be reverted.\n";

        return false;
    }
    */
}
