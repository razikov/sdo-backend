<?php

use yii\db\Migration;

class m190227_120724_create_tbl_custom_user_pwd extends Migration
{
    public function safeUp()
    {
        $this->createTable('custom_user_pwd', [
            'id' => $this->primaryKey(),
            'login' => $this->string()->notNull()->unique(),
            'password' => $this->string()->notNull(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('custom_user_pwd');
    }
}
