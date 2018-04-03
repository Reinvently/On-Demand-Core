<?php
/**
 * @copyright Reinvently (c) 2018
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\components\loggers\migrations;


use yii\db\Migration;

class ConsoleLogMigration extends Migration
{
    public function up()
    {
        $this->createTable(
            'console_log',
            [
                'id' => $this->primaryKey()->unsigned(),
                'startedAt' => $this->integer()->unsigned(),
                'finishedAt' => $this->integer()->unsigned(),
                'route' => $this->string(),
                'request' => $this->text(),
                'response' => $this->text(),
            ]
        );

        $this->createIndex('startedAt', 'console_log', 'startedAt');
        $this->createIndex('route', 'console_log', 'route');

    }

    public function down()
    {
        $this->dropTable('console_log');

        return true;
    }
}