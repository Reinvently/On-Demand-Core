<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\settings\models\extrafee\virtual;
use reinvently\ondemand\core\modules\settings\models\extrafee\ExtraFeeWeekday;

/**
 * Class ExtraFeeWeekdays
 * @package reinvently\ondemand\core\modules\settings\models\extrafee\virtual
 */
class ExtraFeeWeekdays implements ExtraFeeVirtualModel
{
    private $weekdays;
    private $extraFeeId;
    private $errors = [];

    /**
     * ExtraFeeWeekdays constructor.
     * @param array $weekdays
     */
    public function __construct(Array $weekdays)
    {
        $this->weekdays = $weekdays;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param $id
     */
    public function setExtraFeeId($id)
    {
        $this->extraFeeId = $id;
    }

    /**
     * @return bool
     */
    public function validate()
    {
        foreach($this->weekdays as $weekday) {
            /** @var ExtraFeeWeekday $weekdayEntity */
            $weekdayEntity = new ExtraFeeWeekday();
            $weekdayEntity->weekday = $weekday;

            if(!$weekdayEntity->validate()) {
                $this->addErrors($weekdayEntity->getErrors());
                return false;
            }
        }

        return true;
    }

    /**
     * @throws \yii\db\Exception
     */
    public function save()
    {
        $mapToAttributes = function($weekday) {
            return ['extraFeeId' => $this->extraFeeId, 'weekday' => $weekday];
        };

        $rows = array_map($mapToAttributes, $this->weekdays);

        \Yii::$app->db->createCommand()->batchInsert(
            ExtraFeeWeekday::tableName(),
            ['extraFeeId', 'weekday'],
            $rows
        )->execute();
    }

    /**
     *
     */
    public function delete()
    {
        ExtraFeeWeekday::deleteAll(['extraFeeId' => $this->extraFeeId]);
    }

    /**
     * @param $errorsList
     */
    private function addErrors($errorsList)
    {
        foreach($errorsList as $key => $error) {
            $this->addError($key, $error);
        }
    }

    /**
     * @param $key
     * @param $error
     */
    private function addError($key, $error)
    {
        if(!isset($this->errors[$key])) {
            $this->errors[$key] = [$error];
        }
        else{
            $this->errors[$key][] = $error;
        }
    }
}