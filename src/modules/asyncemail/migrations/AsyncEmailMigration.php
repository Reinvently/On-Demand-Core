<?php
/**
 * @copyright Reinvently (c) 2018
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\asyncemail\migrations;


use reinvently\ondemand\core\modules\asyncemail\commands\EmailController;
use reinvently\ondemand\core\vendor\tasker\daemon\Tasker;
use yii\db\Migration;

/**
 * Created by PhpStorm.
 * User: sglushko
 * Date: 28.03.2018
 * Time: 17:03
 */
class AsyncEmailMigration extends Migration
{
    public function up()
    {
        $this->insert('tasker_cyclic_task', [
            'timeInterval' => 5,
            'status' => Tasker::STATUS_READY_TO_RUN,
            'timeNextRun' => 0,
            'cmd' => EmailController::SEND_ALL_BY_SCHEDULER_COMMAND,
        ]);

        $this->createTable('async_email', [
            'id' => $this->primaryKey()->unsigned(),
            'retries' => $this->smallInteger()->unsigned(),
            'status' => $this->smallInteger()->unsigned(),
            'errorText' => $this->string(),
            'version' => $this->integer()->unsigned(),
            'createAt' => $this->integer()->unsigned(),
            'trace' => $this->text(),
            'title' => $this->string(),
            'to' => $this->string(),
            'from' => $this->string(),
            'object' => 'LONGBLOB',
        ]);

        $this->createIndex('status_retries', 'async_email', ['status', 'retries']);
    }

    public function down()
    {
        $this->dropTable('async_email');

        return true;
    }
}