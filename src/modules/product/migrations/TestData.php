<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\modules\product\migrations;


class TestData extends \yii\db\Migration
{
    public function up()
    {
        for ($i = 1; $i <= 30; $i++) {
            $this->insert('product', [
                'id' => $i,
                'title' => 'title' . $i,
                'description' => 'description' . $i,
                'image' => 'image' . $i,
                'sort' => rand(1, 100),
                'price' => rand(1, 100),
                'categoryId' => rand(1, 10),
            ]);
        }
    }
} 