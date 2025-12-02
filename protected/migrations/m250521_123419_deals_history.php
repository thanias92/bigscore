<?php

use yii\db\Migration; // (test1)

/**
 * Class m250521_123419_deals_history
 */
class m250521_123419_deals_history extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%deals_history}}', [ // <--- UBAH DI SINI
            'id' => $this->primaryKey(),
            'deals_id' => $this->integer()->notNull(),
            'old_label' => $this->string(255)->comment('Previous label of the deal'),
            'new_label' => $this->string(255)->comment('New label of the deal'),
            'field_changed' => $this->string(100)->comment('Name of the field that was changed (e.g., label_deals, total, product_id)'),
            'old_value' => $this->text()->comment('Old value of the changed field'),
            'new_value' => $this->text()->comment('New value of the changed field'),
            'activity_type' => $this->string(100)->notNull()->comment('e.g., created, label_changed, field_edited'),
            'description' => $this->text()->notNull()->comment('Detailed description of the activity (e.g., "Label changed to Negotiation")'),
            'created_by' => $this->integer()->comment('User who made the change'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Creates index for column deals_id
        $this->createIndex(
            'idx-deals_history-deals_id', // <--- UBAH DI SINI
            '{{%deals_history}}',          // <--- UBAH DI SINI
            'deals_id'
        );

        // Add foreign key for table {{%deals}}
        $this->addForeignKey(
            'fk-deals_history-deals_id',    // <--- UBAH DI SINI
            '{{%deals_history}}',           // <--- UBAH DI SINI
            'deals_id',
            '{{%deals}}',
            'deals_id',
            'CASCADE',
            'CASCADE'
        );

        // Creates index for column created_by
        $this->createIndex(
            'idx-deals_history-created_by', // <--- UBAH DI SINI
            '{{%deals_history}}',           // <--- UBAH DI SINI
            'created_by'
        );

        // Add foreign key for table {{%user}} (Asumsi tabel user Anda bernama 'user' dan primary key-nya 'id')
        /*
        $this->addForeignKey(
            'fk-deals_history-created_by', // <--- UBAH DI SINI
            '{{%deals_history}}',          // <--- UBAH DI SINI
            'created_by',
            '{{%user}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
        */
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Drop foreign key for table {{%user}}
        /*
        $this->dropForeignKey(
            'fk-deals_history-created_by', // <--- UBAH DI SINI
            '{{%deals_history}}'           // <--- UBAH DI SINI
        );

        // Drop index for column created_by
        $this->dropIndex(
            'idx-deals_history-created_by', // <--- UBAH DI SINI
            '{{%deals_history}}'           // <--- UBAH DI SINI
        );
        */

        // Drop foreign key for table {{%deals}}
        $this->dropForeignKey(
            'fk-deals_history-deals_id',    // <--- UBAH DI SINI
            '{{%deals_history}}'            // <--- UBAH DI SINI
        );

        // Drop index for column deals_id
        $this->dropIndex(
            'idx-deals_history-deals_id',   // <--- UBAH DI SINI
            '{{%deals_history}}'            // <--- UBAH DI SINI
        );

        $this->dropTable('{{%deals_history}}'); // <--- UBAH DI SINI
    }
}
