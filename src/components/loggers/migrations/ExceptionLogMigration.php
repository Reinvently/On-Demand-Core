<?php
/**
 * @copyright Reinvently (c) 2018
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\components\loggers\migrations;

use yii\db\Migration;

/**
 * Class ExceptionLogMigration
 */
class ExceptionLogMigration extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('exception_log', [
            'id' => $this->primaryKey()->unsigned(),
            'datetime' => $this->integer()->unsigned(),
            'route' => $this->string(),
            'userId' => $this->integer()->unsigned(),
            'message' => $this->text(),
            'fileName' => $this->string(),
            'lineFile' => $this->smallInteger()->unsigned(),
            'stackTrace' => $this->text(),
            'isFailed' => $this->boolean(),
            'ip' => $this->integer()->unsigned(),
            'request' => $this->text(),
        ]);

    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('exception_log');

        return true;
    }
}
