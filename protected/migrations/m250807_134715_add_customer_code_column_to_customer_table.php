<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%customer}}`.
 */
class m250807_134715_add_customer_code_column_to_customer_table extends Migration
{
    public function safeUp()
    {
        // Menambahkan kolom baru 'customer_code' dengan tipe STRING dan NOT NULL.
        // PENTING: defaultValue('') ditambahkan agar migrasi tidak error jika tabel sudah memiliki data.
        $this->addColumn('{{%customer}}', 'customer_code', $this->string()->notNull()->defaultValue(''));
    }

    public function safeDown()
    {
        // Menghapus kolom 'customer_code' jika migrasi di-revert.
        $this->dropColumn('{{%customer}}', 'customer_code');
    }
}
