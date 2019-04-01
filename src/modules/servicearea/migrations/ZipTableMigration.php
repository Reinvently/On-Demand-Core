<?php
/**
 * @copyright Reinvently (c) 2018
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\servicearea\migrations;

use yii\db\Migration;

/**
 * Class ZipTableMigration
 */
class ZipTableMigration extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('zip', [
            'id' => $this->primaryKey(),
            'country' => $this->string()->notNull(),
            'zip' => $this->string()->notNull(),
            'stateCode' => $this->string(),
            'city' => $this->string(),
            'serviceAreaId' => $this->integer()->unsigned(),
        ]);

        $this->createIndex('country_zip', 'zip', ['country', 'zip'], true);
        $this->createIndex('zip', 'zip', 'zip', true);

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "ZipTableMigration cannot be reverted.\n";

        return false;
    }
}
