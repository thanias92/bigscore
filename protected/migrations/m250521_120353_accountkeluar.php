<?php

use yii\db\Migration;

/**
 * Class m250521_120353_accountkeluar
 */
class m250521_120353_accountkeluar extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%accountkeluar}}', [
            'id'           => $this->primaryKey(),
            'parent_id'    => $this->integer()->null()->comment('Relasi ke akun induk'),
            'code'         => $this->string()->notNull()->comment('Kode akun, bisa huruf dan angka'),
            'akun'         => $this->string()->notNull()->comment('Nama akun'),
            'penggunaan'   => $this->string()->notNull()->comment('pengeluaran / pemasukan'),
            'created_by'   => $this->integer(),
            'updated_by'   => $this->integer(),
            'deleted_by'   => $this->integer(),
            'created_at'   => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at'   => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'deleted_at'   => $this->timestamp()->defaultValue(null),
        ]);

        // Tambahkan indeks dan foreign key untuk parent_id (relasi ke diri sendiri)
        $this->createIndex(
            'idx-accountkeluar-parent_id',
            '{{%accountkeluar}}',
            'parent_id'
        );

        $this->addForeignKey(
            'fk-accountkeluar-parent_id',
            '{{%accountkeluar}}',
            'parent_id',
            '{{%accountkeluar}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        // Trigger untuk update kolom updated_at saat record diubah
        $this->execute("
            CREATE OR REPLACE FUNCTION update_accountkeluar_updated_at()
            RETURNS TRIGGER AS $$
            BEGIN
                NEW.updated_at = NOW();
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ");

        $this->execute("
            CREATE TRIGGER trg_update_accountkeluar_updated_at
            BEFORE UPDATE ON {{%accountkeluar}}
            FOR EACH ROW
            EXECUTE FUNCTION update_accountkeluar_updated_at();
        ");
    }

    public function safeDown()
    {
        $this->execute("DROP TRIGGER IF EXISTS trg_update_accountkeluar_updated_at ON {{%accountkeluar}}");
        $this->execute("DROP FUNCTION IF EXISTS update_accountkeluar_updated_at");

        $this->dropForeignKey('fk-accountkeluar-parent_id', '{{%accountkeluar}}');
        $this->dropIndex('idx-accountkeluar-parent_id', '{{%accountkeluar}}');

        $this->dropTable('{{%accountkeluar}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250521_120353_accountkeluar cannot be reverted.\n";

        return false;
    }
    */
}
