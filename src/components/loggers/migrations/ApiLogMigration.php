<?php
/**
 * @copyright Reinvently (c) 2018
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */
namespace reinvently\ondemand\core\components\loggers\migrations;

use yii\db\Migration;

class ApiLogMigration extends Migration
{
    public function up()
    {
        $this->createTable(
            'api_log',
            [
                'id' => $this->primaryKey()->unsigned(),
                'userId' => $this->integer()->unsigned(),
                'token' => $this->string(),
                'startedAt' => $this->integer()->unsigned(),
                'finishedAt' => $this->integer()->unsigned(),
                'route' => $this->string(),
                'request' => $this->text(),
                'response' => $this->text(),
                'ip' => $this->bigInteger(),
            ]
        );

        $this->createIndex('userId', 'api_log', 'userId');
        $this->createIndex('token', 'api_log', 'token');
        $this->createIndex('startedAt', 'api_log', 'startedAt');
        $this->createIndex('route', 'api_log', 'route');
        $this->createIndex('ip', 'api_log', 'ip');

    }

    public function down()
    {
        $this->dropTable('api_log');

        return true;
    }
}
