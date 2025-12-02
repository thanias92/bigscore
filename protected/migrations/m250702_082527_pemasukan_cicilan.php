<?php

use yii\db\Migration;

/**
 * Class m250702_082527_pemasukan_cicilan
 */
class m250702_082527_pemasukan_cicilan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('pemasukan_cicilan', [
            'id'            => $this->primaryKey(),
            'pemasukan_id'  => $this->integer()->notNull(),
            'ke'            => $this->integer()->notNull(),        // angsuran ke‑1,2,…
            'jatuh_tempo'   => $this->date()->notNull(),
            'nominal'       => $this->decimal(20, 2)->notNull(),
            'status'        => $this->string(20)->notNull()->defaultValue('Menunggu'),
            'tanggal_bayar' => $this->date()->null(),
            'bukti_path'    => $this->string()->null(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'deleted_at' => $this->timestamp()->null(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
            'deleted_by' => $this->integer(),
        ]);

        $this->addForeignKey(
            'fk_cicilan_pemasukan',
            'pemasukan_cicilan',
            'pemasukan_id',
            'pemasukan',
            'pemasukan_id',
            'CASCADE',
            'CASCADE'
        );

        $this->createIndex(
            'ux_cicilan_unik',
            'pemasukan_cicilan',
            ['pemasukan_id', 'ke'],
            true
        );
    }
    public function safeDown()
    {
        $this->dropTable('pemasukan_cicilan');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250702_082527_pemasukan_cicilan cannot be reverted.\n";

        return false;
    }
    */
}
