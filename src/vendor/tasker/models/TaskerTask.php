<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

/**
 * Created by PhpStorm.
 * User: sglushko
 * Date: 19.10.2017
 * Time: 13:45
 */

namespace reinvently\ondemand\core\vendor\tasker\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "tasker_task".
 *
 * @property string $id
 * @property string $timeNextRun
 * @property integer $status
 * @property string $timeLastStatus
 * @property string $cmd
 * @property string $data
 * @property string $log
 */
class TaskerTask extends \yii\db\ActiveRecord
{
    static public function getStatusStrings()
    {
        return [
            null => 'All',
            \reinvently\ondemand\core\vendor\tasker\daemon\Tasker::TASK_STATUS_WAITING => 'WAITING',
            \reinvently\ondemand\core\vendor\tasker\daemon\Tasker::TASK_STATUS_READY_TO_RUN => 'READY_TO_RUN',
            \reinvently\ondemand\core\vendor\tasker\daemon\Tasker::TASK_STATUS_LAUNCHED => 'LAUNCHED',
            \reinvently\ondemand\core\vendor\tasker\daemon\Tasker::TASK_STATUS_FINISHED_SUCCESSFUL => 'FINISHED_SUCCESSFUL',
            \reinvently\ondemand\core\vendor\tasker\daemon\Tasker::TASK_STATUS_FAILED => 'FAILED',
            \reinvently\ondemand\core\vendor\tasker\daemon\Tasker::TASK_STATUS_CANCELED => 'CANCELED',
        ];
    }

    static public function getStatusString($status)
    {
        return ArrayHelper::getValue(static::getStatusStrings(), $status);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tasker_task';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['timeNextRun', 'status', 'timeLastStatus'], 'integer'],
            [['data', 'log'], 'string'],
            [['cmd'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'timeNextRun' => 'Time Next Run',
            'status' => 'Status',
            'timeLastStatus' => 'Time Last Status',
            'cmd' => 'Cmd',
            'data' => 'Data',
            'log' => 'Log',
        ];
    }
}
