<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\modules\payment\migrations;


use yii\db\Migration;

class TestData extends Migration
{
    public function up()
    {
        for ($i = 1; $i <= 3; $i++) {
            $this->insert('payment', [
                'id' => $i,
                'orderId' => rand(1, 100),
                'price' => rand(1, 10000),
                'status' => rand(1, 5),
                'description' => 'description' . $i,
                'transactionId' => rand(100000, 999999),
                'createdAt' => time() + rand(100000, 999999),
                'updatedAt' => time() + rand(100000, 999999),
            ]);
        }
    }

} 