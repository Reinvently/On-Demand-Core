<?php
/**
 * @copyright Reinvently (c) 2019
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\pushnotification\migrations;

use reinvently\ondemand\core\modules\pushnotification\commands\PushNotificationController;
use reinvently\ondemand\core\vendor\tasker\daemon\Tasker;
use yii\db\Migration;

class AsyncPushNotificationMigration extends Migration
{
    public function up()
    {
        $this->createTable('push_notification', [
            'id' => $this->primaryKey()->unsigned(),
            'retries' => $this->smallInteger()->unsigned(),
            'status' => $this->smallInteger()->unsigned(),
            'errorText' => $this->string(),
            'version' => $this->integer()->unsigned(),
            'createAt' => $this->integer()->unsigned(),
            'trace' => $this->text(),
            'json' => $this->text(),
            'toUserId' => $this->integer()->unsigned(),
            'toClientId' => $this->integer()->unsigned(),
            'object' => 'LONGBLOB',
        ]);

        $this->createIndex('status_retries', 'push_notification', ['status', 'retries']);

        $this->createTable('client_push_token', [
            'id' => $this->primaryKey()->unsigned(),
            'clientId' => $this->integer()->unsigned(),
            'userId' => $this->integer()->unsigned(),
            'token' => $this->string(),
            'createdAt' => $this->integer()->unsigned(),
            'updatedAt' => $this->integer()->unsigned(),
            'platform' => $this->string(),
            'application' => $this->string(),
            'applicationVersion' => $this->string(),
            'authorizedEntity' => $this->string(),
        ]);

        $this->createIndex('clientId', 'client_push_token', ['clientId']);
        $this->createIndex('userId', 'client_push_token', ['userId']);
        $this->createIndex('token', 'client_push_token', ['token']);

        $this->insert('tasker_cyclic_task', [
            'timeInterval' => 5,
            'status' => Tasker::STATUS_READY_TO_RUN,
            'timeNextRun' => 0,
            'cmd' => PushNotificationController::SEND_ALL_BY_SCHEDULER_COMMAND,
        ]);
    }

    public function down()
    {
        return false;
    }

}