<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\modules\orderproduct\migrations;


use yii\db\Migration;

class OrderProductTableMigration extends Migration
{
    public function up()
    {
        $this->createTable('order_product', [
            'id' => $this->integer() . ' UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'orderId' => $this->integer() . ' UNSIGNED',
            'productId' => $this->integer() . ' UNSIGNED',
            'price' => $this->integer() . ' UNSIGNED',
            'count' => $this->integer() . ' UNSIGNED',
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci');

        $this->addColumn('order_product', 'title', $this->string());
        $this->addColumn('order_product', 'description', $this->text());
        $this->addColumn('order_product', 'categoryId', $this->integer()->unsigned());
        $this->addColumn('order_product', 'shortDescription', $this->string());
        $this->addColumn('order_product', 'isOneTimePay', $this->boolean());


        $this->createIndex('orderId_productId', 'order_product', ['orderId', 'productId'], true);
    }

    public function down()
    {
        $this->dropTable('order_product');
        return true;
    }

} 