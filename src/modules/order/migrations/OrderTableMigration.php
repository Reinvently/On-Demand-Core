<?php
/**
 * @copyright Reinvently (c) 2018
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\order\migrations;

use reinvently\ondemand\core\modules\order\models\Order;
use Yii;
use yii\db\Migration;

class OrderTableMigration extends Migration
{
    public function up()
    {
        $this->createTable('order', [
            'id' => $this->integer() . ' UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'status' => $this->string(),
            'userId' => $this->integer() . ' UNSIGNED',
            'addressId' => $this->integer() . ' UNSIGNED',
            'serviceAreaId' => $this->integer()->unsigned(),
            'firstName' => $this->string(),
            'lastName' => $this->string(),
            'phone' => $this->string(),
            'v' => $this->integer() . ' UNSIGNED',
            'createdAt' => $this->integer() . ' UNSIGNED NOT NULL',
            'updatedAt' => $this->integer() . ' UNSIGNED NOT NULL'
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci');

        /*$this->insert('order', [
            'status' => Order::STATUS_ACTIVE,
            'userId' => null,
            'v' => 1,
            'firstName' => 'Test Name',
            'lastName' => 'Test LAst Name',
            'createdAt' => time(),
            'updatedAt' => time()
        ]);*/

    }

    public function down()
    {
        $this->dropTable('order');
        return true;
    }
}
