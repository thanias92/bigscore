<?php

use yii\db\Migration;

/**
 * Class m250807_103806_alter_tabel_tambahduedate
 */
class m250807_103806_alter_tabel_tambahduedate extends Migration
{
    /**
     * {@inheritdoc}
     */
     public function safeUp()
    {
        // Cek apakah kolom 'duedate' belum ada di tabel 'ticket'
        $table = $this->db->schema->getTableSchema('{{%ticket}}');
        if (!isset($table->columns['duedate'])) {
            $this->addColumn('{{%ticket}}', 'duedate', $this->date()->after('date_ticket'));
        } else {
            echo "Kolom 'duedate' sudah ada dalam tabel 'ticket'.\n";
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Cek apakah kolom 'duedate' ada sebelum menghapus
        $table = $this->db->schema->getTableSchema('{{%ticket}}');
        if (isset($table->columns['duedate'])) {
            $this->dropColumn('{{%ticket}}', 'duedate');
        } else {
            echo "Kolom 'duedate' tidak ditemukan dalam tabel 'ticket'.\n";
        }
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250807_103806_alter_tabel_tambahduedate cannot be reverted.\n";

        return false;
    }
    */
}
