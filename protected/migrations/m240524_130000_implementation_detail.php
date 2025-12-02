<?php

use yii\db\Migration;

class m240524_130000_implementation_detail extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%implementation_detail}}', [
            'id_implementasi_detail' => $this->primaryKey(), // auto-increment primary key
            'id_implementasi' => $this->integer()->notNull(),
            'activity' => $this->string(255),
            'detail' => $this->text(),
            'start_date' => $this->date(),
            'completion_date' => $this->date(),
            'pic_aktivitas' => $this->string(255),
            'notes' => $this->text(),
            'duration' => $this->time(),
            'status' => $this->string()->notNull(),

            'created_by' => $this->integer()->defaultValue(null),
            'updated_by' => $this->integer()->defaultValue(null),
            'deleted_by' => $this->integer()->defaultValue(null),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'deleted_at' => $this->timestamp()->defaultValue(null),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%implementation_detail}}');
    }
}
