<?php

use yii\db\Migration;

/**
 * Class m250725_000000_add_id_task_to_sub_task
 */
class m250725_000000_alter_sub_task extends Migration
{
    public function safeUp()
    {
        // Tambah kolom id_task ke tabel sub_task
        $this->addColumn('{{%sub_task}}', 'id_task', $this->integer()->defaultValue(null));

        // Tambah foreign key jika id_task merujuk ke tabel task
        $this->addForeignKey(
            'fk-sub_task-id_task',
            '{{%sub_task}}',
            'id_task',
            '{{%task}}',
            'id_task',
            'SET NULL',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        // Hapus foreign key dulu sebelum hapus kolom
        $this->dropForeignKey('fk-sub_task-id_task', '{{%sub_task}}');
        $this->dropColumn('{{%sub_task}}', 'id_task');
    }
}
