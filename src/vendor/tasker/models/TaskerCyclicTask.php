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

/**
 * This is the model class for table "tasker_cyclic_task".
 *
 * @property string $id
 * @property string $timeLastRun
 * @property string $timeInterval
 * @property string $timeNextRun
 * @property integer $status
 * @property string $timeLastStatus
 * @property string $cmd
 * @property string $data
 * @property string $log
 */
class TaskerCyclicTask extends TaskerTask
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tasker_cyclic_task';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['timeNextRun', 'status', 'timeLastStatus', 'timeLastRun', 'timeInterval'], 'integer'],
            [['data', 'log'], 'string'],
            [['cmd'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'timeLastRun' => 'Time Last Run',
            'timeInterval' => 'Time Interval',
        ]);
    }
}
