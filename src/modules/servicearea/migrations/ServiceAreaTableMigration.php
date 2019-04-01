<?php
/**
 * @copyright Reinvently (c) 2018
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\modules\servicearea\migrations;

use yii\db\Migration;

/**
 * Class ServiceAreaTableMigration
 */
class ServiceAreaTableMigration extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('service_area', [
            'id' => $this->primaryKey(),
            'type' => $this->smallInteger()->unsigned(),
            'status' => $this->smallInteger()->unsigned(),
            'name' => $this->string(),
        ]);

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "ServiceAreaTableMigration cannot be reverted.\n";

        return false;
    }
}
