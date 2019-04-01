<?php
/**
 * @copyright Reinvently (c) 2018
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\resource\migrations;

use yii\db\Migration;

/**
 * Class ResourceTableMigration
 * @package reinvently\ondemand\core\modules\resource\migrations
 */
class ResourceTableMigration extends Migration
{
    /**
     * @return bool
     */
    public function up()
    {
        $this->createTable('resource', [
            'id' => $this->primaryKey(),
            'type' => $this->integer()->unsigned(),
            'title' => $this->string(),
            'description' => $this->text(),
            'alias' => $this->string(),
            'extension' => $this->string(),
            'createAt' => $this->integer()->unsigned(),
            'updateAt' => $this->integer()->unsigned(),
            'version' => $this->integer()->unsigned(),
        ]);

    }

    /**
     * @return bool
     */
    public function down()
    {
        $this->dropTable('resource');
        return true;
    }


}