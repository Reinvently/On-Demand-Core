<?php

use yii\db\Migration;

class SettingTableMigration extends Migration
{
    public function up()
    {
        $this->createTable('setting', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'value' => $this->string(),
        ]);
    }

    public function down()
    {
        $this->dropTable('setting');
        return true;
    }
}
