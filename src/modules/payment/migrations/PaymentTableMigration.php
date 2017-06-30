<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\modules\payment\migrations;


use yii\db\Migration;

class PaymentTableMigration extends Migration
{
    public function up()
    {
        $this->createTable('payment', [
            // todo https://github.com/yiisoft/yii2/issues/9929
            'id' => $this->integer() . ' UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'orderId' => $this->integer() . ' UNSIGNED',
            'price' => $this->integer() . ' UNSIGNED',
            'status' => $this->smallInteger() . ' UNSIGNED',
            'createdAt' => $this->integer() . ' UNSIGNED NOT NULL',
            'updatedAt' => $this->integer() . ' UNSIGNED NOT NULL',
            'transactionId' => $this->string(),
            'description' => $this->string(),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci');

        $this->createIndex('orderId', 'payment', 'orderId');
        $this->createIndex('status', 'payment', 'status');
        $this->createIndex('createdAt', 'payment', 'createdAt');
        $this->createIndex('updatedAt', 'payment', 'updatedAt');
    }

    public function down()
    {
        $this->dropTable('payment');
        return true;
    }

} 