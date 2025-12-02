<?php

use yii\db\Migration;

/**
 * Handles dropping columns from table `{{%quotation}}`.
 */
class m250725_155746_drop_quotation_file_column_from_quotation_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('{{%quotation}}', 'quotation_file');
    }

    public function safeDown()
    {
        $this->addColumn('{{%quotation}}', 'quotation_file', $this->string());
    }
}
