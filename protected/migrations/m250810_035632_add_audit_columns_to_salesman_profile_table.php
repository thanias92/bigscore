<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%salesman_profile}}`.
 */
class m250810_035632_add_audit_columns_to_salesman_profile_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%salesman_profile}}', 'created_by', $this->integer());
        $this->addColumn('{{%salesman_profile}}', 'updated_by', $this->integer());

        // Opsional: Tambahkan foreign key jika Anda ingin
        $this->addForeignKey(
            'fk-salesman_profile-created_by',
            '{{%salesman_profile}}',
            'created_by',
            '{{%user}}',
            'id',
            'SET NULL'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-salesman_profile-created_by', '{{%salesman_profile}}');
        $this->dropColumn('{{%salesman_profile}}', 'created_by');
        $this->dropColumn('{{%salesman_profile}}', 'updated_by');
    }
}
