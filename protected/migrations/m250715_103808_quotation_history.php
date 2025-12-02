<?php

use yii\db\Migration;

/**
 * Class m250715_103808_quotation_history
 */
class m250715_103808_quotation_history extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%quotation_history}}', [
            'id' => $this->primaryKey(),
            'quotation_id' => $this->integer()->notNull(),

            'field_changed' => $this->string(100)->comment('Field yang diubah'),
            'old_value' => $this->text()->comment('Nilai sebelum diubah'),
            'new_value' => $this->text()->comment('Nilai setelah diubah'),

            'activity_type' => $this->string(100)->notNull()->comment('Jenis aktivitas (e.g. created, updated, status_changed)'),
            'description' => $this->text()->notNull()->comment('Penjelasan aktivitas'),

            'created_by' => $this->integer(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Index & FK
        $this->createIndex('idx-quotation_history-quotation_id', '{{%quotation_history}}', 'quotation_id');
        $this->addForeignKey(
            'fk-quotation_history-quotation_id',
            '{{%quotation_history}}',
            'quotation_id',
            '{{%quotation}}',
            'quotation_id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-quotation_history-quotation_id', '{{%quotation_history}}');
        $this->dropIndex('idx-quotation_history-quotation_id', '{{%quotation_history}}');
        $this->dropTable('{{%quotation_history}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250715_103808_quotation_history cannot be reverted.\n";

        return false;
    }
    */
}
