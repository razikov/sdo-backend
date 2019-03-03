<?php

use yii\db\Migration;

/**
 * Class m181202_210018_create_tbl_uploads
 */
class m181202_210018_create_tbl_uploads extends Migration
{
    public function safeUp()
    {
        $this->createTable('uploads', [
            'id' => $this->primaryKey(),
            'type' => $this->smallInteger()->notNull(),
            'filename' => $this->string()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('uploads');
    }
}
