<?php
/**
 * @copyright Reinvently (c) 2018
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\modules\product\migrations;


use yii\db\Migration;

class ProductTableMigration extends Migration
{
    public function up()
    {
        $this->createTable('product', [
            // todo https://github.com/yiisoft/yii2/issues/9929
            'id' => $this->integer() . ' UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'title' => $this->string(),
            'description' => $this->text(),
            'categoryId' => $this->integer() . ' UNSIGNED',
            'sort' => $this->integer() . ' UNSIGNED',
            'price' => $this->integer() . ' UNSIGNED',
            'serviceAreaId' => $this->integer()->unsigned(),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci');

        $this->addColumn('product', 'shortDescription', $this->string());
        $this->addColumn('product', 'isOneTimePay', $this->boolean());

        $this->createIndex('categoryId', 'product', 'categoryId');
        $this->createIndex('sort', 'product', 'sort');
    }

    public function down()
    {
        $this->dropTable('product');
        return true;
    }

} 