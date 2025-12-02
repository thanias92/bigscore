<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%deal_quotations_junction}}`.
 */
class m250729_161940_create_deal_quotations_junction_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%deal_quotations}}', [
            'id' => $this->primaryKey(),
            'deal_id' => $this->integer()->notNull(),
            'quotation_id' => $this->integer()->notNull(),
            'is_active' => $this->boolean()->defaultValue(true)->comment('Menandai quotation yang berlaku saat ini'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // --- Foreign Key untuk ke Tabel 'deals' ---

        // Buat index untuk kolom `deal_id`
        $this->createIndex(
            '{{%idx-deal_quotations-deal_id}}',
            '{{%deal_quotations}}',
            'deal_id'
        );

        // Tambah foreign key
        $this->addForeignKey(
            '{{%fk-deal_quotations-deal_id}}',
            '{{%deal_quotations}}',
            'deal_id',
            '{{%deals}}', // Nama tabel deals Anda
            'deals_id', // KOREKSI: Mereferensikan ke 'deals_id' di tabel 'deals'
            'CASCADE'
        );


        // --- Foreign Key untuk ke Tabel 'quotation' ---

        // Buat index untuk kolom `quotation_id`
        $this->createIndex(
            '{{%idx-deal_quotations-quotation_id}}',
            '{{%deal_quotations}}',
            'quotation_id'
        );

        // Tambah foreign key
        $this->addForeignKey(
            '{{%fk-deal_quotations-quotation_id}}',
            '{{%deal_quotations}}',
            'quotation_id',
            '{{%quotation}}', // KOREKSI: Nama tabel quotation (singular)
            'quotation_id', // KOREKSI: Mereferensikan ke 'quotation_id' di tabel 'quotation'
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Hapus foreign key untuk 'deals'
        $this->dropForeignKey(
            '{{%fk-deal_quotations-deal_id}}',
            '{{%deal_quotations}}'
        );

        // Hapus index untuk 'deal_id'
        $this->dropIndex(
            '{{%idx-deal_quotations-deal_id}}',
            '{{%deal_quotations}}'
        );

        // Hapus foreign key untuk 'quotation'
        $this->dropForeignKey(
            '{{%fk-deal_quotations-quotation_id}}',
            '{{%deal_quotations}}'
        );

        // Hapus index untuk 'quotation_id'
        $this->dropIndex(
            '{{%idx-deal_quotations-quotation_id}}',
            '{{%deal_quotations}}'
        );

        $this->dropTable('{{%deal_quotations}}');
    }
}
