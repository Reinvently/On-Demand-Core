<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\vendor\tasker\migrations;

use yii\db\Migration;

/**
 * Created by PhpStorm.
 * User: sglushko
 * Date: 19.10.2017
 * Time: 13:45
 */
class TaskerMigrations extends Migration
{
    public function up()
    {
        $this->createTable('tasker_params', [
            'id' => $this->primaryKey()->unsigned(),
            'name' => $this->string(),
            'value' => $this->string(),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci');

        $this->batchInsert('tasker_params', ['id', 'name', 'value'], [
            [1, 'Update Interval', 60],
            [2, 'Number of Tasker', 2],
            [3, 'Tasker Sleep Interval', 5],
        ]);

        $this->createTable('tasker', [
            'id' => $this->primaryKey()->unsigned(),
            'status' => $this->smallInteger()->unsigned()->notNull(),
            'timeStart' => $this->integer()->unsigned()->notNull(),
            'timeLastActivity' => $this->integer()->unsigned()->notNull(),
            'processId' => $this->integer()->unsigned()->notNull(),
            'currentTaskId' => $this->integer()->unsigned(),
            'currentCyclicTaskId' => $this->integer()->unsigned(),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci');

        $this->createTable('tasker_cyclic_task', [
            'id' => $this->primaryKey()->unsigned(),
            'timeLastRun' => $this->integer()->unsigned(),
            'timeInterval' => $this->integer()->unsigned(),
            'timeNextRun' => $this->integer()->unsigned(),
            'status' => $this->smallInteger()->unsigned(),
            'timeLastStatus' => $this->integer()->unsigned(),
            'cmd' => $this->string(),
            'data' => $this->text(),
            'log' => $this->text(),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci');

        $this->createTable('tasker_task', [
            'id' => $this->primaryKey()->unsigned(),
            'timeNextRun' => $this->integer()->unsigned(),
            'status' => $this->smallInteger()->unsigned(),
            'timeLastStatus' => $this->integer()->unsigned(),
            'cmd' => $this->string(),
            'data' => $this->text(),
            'log' => $this->text(),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci');

        $this->createIndex('timeNextRun', 'tasker_cyclic_task', ['timeNextRun']);
        $this->createIndex('timeNextRun', 'tasker_task', ['timeNextRun']);

        $this->batchInsert('tasker_cyclic_task', [
            'timeInterval', 'timeNextRun', 'status', 'cmd', 'data'
        ], [
            [5, 0, 1, 'php yii hello', null],
            [5, 0, 1, 'php yii hello', 'test message'],
            [5, 0, 1, 'php yii test error command', null],
        ]);

        $this->batchInsert('tasker_task', [
            'timeNextRun', 'status', 'cmd', 'data'
        ], [
            [0, 1, 'php yii hello', null],
            [0, 1, 'php yii hello', 'test message'],
            [0, 1, 'php yii test error command', null],
        ]);

    }

    public function down()
    {
        $this->dropTable('tasker_params');
        $this->dropTable('tasker');
        $this->dropTable('tasker_cyclic_task');
        $this->dropTable('tasker_task');
        return true;
    }
}