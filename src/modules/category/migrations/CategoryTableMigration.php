<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\modules\category\migrations;


use yii\db\Migration;

class CategoryTableMigration extends Migration
{
    public function up()
    {
        $this->createTable('category', [
            // todo https://github.com/yiisoft/yii2/issues/9929
            'id' => $this->integer() . ' UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'title' => $this->string(),
            'description' => $this->text(),
            'image' => $this->string(),
            'parentId' => $this->integer() . ' UNSIGNED',
            'sort' => $this->integer() . ' UNSIGNED',
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci');

        $this->createIndex('parentId', 'category', 'parentId');
        $this->createIndex('sort', 'category', 'sort');
    }

    public function down()
    {
        $this->dropTable('category');
        return true;
    }
}
