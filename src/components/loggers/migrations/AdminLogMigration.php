<?php
/**
 * @copyright Reinvently (c) 2018
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\components\loggers\migrations;


use yii\db\Migration;

class AdminLogMigration extends Migration
{
    public function up()
    {
        $this->createTable(
            'admin_log',
            [
                'id' => $this->primaryKey()->unsigned(),
                'userId' => $this->integer()->unsigned(),
                'startedAt' => $this->integer()->unsigned(),
                'finishedAt' => $this->integer()->unsigned(),
                'route' => $this->string(),
                'request' => $this->text(),
                'ip' => $this->bigInteger(),
            ]
        );

        $this->createIndex('userId', 'admin_log', 'userId');
        $this->createIndex('startedAt', 'admin_log', 'startedAt');
        $this->createIndex('route', 'admin_log', 'route');
        $this->createIndex('ip', 'admin_log', 'ip');
    }

    public function down()
    {
        $this->dropTable('admin_log');

        return true;
    }
}
