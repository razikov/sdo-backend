<?php

use yii\db\Migration;

class m190227_120705_create_tbl_custom_import_user extends Migration
{
    public function safeUp()
    {
        $this->createTable('custom_import_user', [
            'id' => $this->primaryKey(),
            'upload_id' => $this->integer()->notNull(),
            'usersJson' => $this->text(),
            'step' => $this->string()->notNull(),
            'role_ids' => $this->string(),
            'upload_id_xml' => $this->integer(),
            'upload_id_xls' => $this->integer(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('custom_import_user');
    }
}
