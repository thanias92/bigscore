<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%salesman_profile}}`.
 */
class m250810_034825_create_salesman_profile_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%salesman_profile}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull()->unique(),
            'visit_target' => $this->integer()->notNull()->defaultValue(20),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);

        // Membuat index untuk kolom user_id untuk mempercepat pencarian
        $this->createIndex(
            '{{%idx-salesman_profile-user_id}}',
            '{{%salesman_profile}}',
            'user_id'
        );

        // Menambahkan foreign key dari tabel `salesman_profile` ke tabel `user`
        $this->addForeignKey(
            '{{%fk-salesman_profile-user_id}}',
            '{{%salesman_profile}}',
            'user_id',
            '{{%user}}', // Pastikan nama tabel user Anda adalah 'user'
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        // Drop foreign key terlebih dahulu
        $this->dropForeignKey(
            '{{%fk-salesman_profile-user_id}}',
            '{{%salesman_profile}}'
        );

        // Drop index
        $this->dropIndex(
            '{{%idx-salesman_profile-user_id}}',
            '{{%salesman_profile}}'
        );

        // Drop table
        $this->dropTable('{{%salesman_profile}}');
    }
}
