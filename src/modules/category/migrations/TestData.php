<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\modules\category\migrations;


class TestData extends \yii\db\Migration
{
    public function up()
    {
        for ($i = 1; $i <= 10; $i++) {
            $this->insert('category', [
                'id' => $i,
                'title' => 'title' . $i,
                'description' => 'description' . $i,
                'image' => 'image' . $i,
                'sort' => rand(1, 100),
            ]);
        }
    }
} 