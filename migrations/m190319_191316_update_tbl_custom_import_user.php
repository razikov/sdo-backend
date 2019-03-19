<?php

use yii\db\Migration;

class m190319_191316_update_tbl_custom_import_user extends Migration
{
    const TABLE_NAME = 'custom_import_user';

    public function safeUp()
    {
        $this->renameColumn(self::TABLE_NAME, 'usersJson', 'users_json');
        $this->alterColumn(self::TABLE_NAME, 'users_json', 'MEDIUMTEXT');
        $this->addColumn(self::TABLE_NAME, 'created_at', $this->dateTime());
        $this->addColumn(self::TABLE_NAME, 'name', $this->string());
    }

    public function safeDown()
    {
        $this->dropColumn(self::TABLE_NAME, 'name');
        $this->dropColumn(self::TABLE_NAME, 'created_at');
        $this->alterColumn(self::TABLE_NAME, 'users_json', $this->text());
        $this->renameColumn(self::TABLE_NAME, 'users_json', 'usersJson');
    }

}
