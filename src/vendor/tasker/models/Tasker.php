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
 * This is the model class for table "tasker".
 *
 * @property string $id
 * @property integer $status
 * @property string $timeStart
 * @property string $timeLastActivity
 * @property string $processId
 * @property string $currentTaskId
 * @property string $currentCyclicTaskId
 */
class Tasker extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tasker';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'timeStart', 'timeLastActivity', 'processId'], 'required'],
            [['status', 'timeStart', 'timeLastActivity', 'processId', 'currentTaskId', 'currentCyclicTaskId'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'status' => 'Status',
            'timeStart' => 'Time Start',
            'timeLastActivity' => 'Time Last Activity',
            'processId' => 'Process ID',
            'currentTaskId' => 'Current Task ID',
            'currentCyclicTaskId' => 'Current Cyclic Task ID',
        ];
    }
}
