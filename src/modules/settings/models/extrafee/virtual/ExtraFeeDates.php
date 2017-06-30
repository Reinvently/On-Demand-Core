<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\settings\models\extrafee\virtual;

use reinvently\ondemand\core\exceptions\ExtraFeeDatesNotSetException;
use reinvently\ondemand\core\modules\settings\models\extrafee\ExtraFeeMonth;
use reinvently\ondemand\core\modules\settings\models\extrafee\ExtraFeeYear;
use reinvently\ondemand\core\modules\settings\models\extrafee\ExtraFeeTime;

/**
 * Class ExtraFeeDates
 * @package reinvently\ondemand\core\modules\settings\models\extrafee\virtual
 */
class ExtraFeeDates implements ExtraFeeVirtualModel
{
    private $days;
    private $dates;
    private $extraFeeId;
    private $errors = [];

    /**
     * ExtraFeeDates constructor.
     */
    public function __construct()
    {
        $this->days = new ExtraFeeDays;
    }

    /**
     * @param array $dates
     */
    public function setDates(Array $dates)
    {
        $this->dates = $dates;
        $this->days->setDates($dates);
    }

    /**
     * @param $id
     */
    public function setExtraFeeId($id)
    {
        $this->extraFeeId = $id;
        $this->days->setExtraFeeId($id);
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return array_merge($this->errors, $this->days->getErrors());
    }

    /**
     * @return bool
     * @throws ExtraFeeDatesNotSetException
     */
    public function validate()
    {
        if (empty($this->dates)) {
            throw new ExtraFeeDatesNotSetException;
        }

        $dateEntities = ['years', 'months', 'timeStart', 'timeFinish'];

        foreach ($dateEntities as $entity) {
            if (!isset($this->dates[$entity]) or empty($this->dates[$entity])) {
                $this->addError($entity, 'Can not be empty');
                return false;
            }
        }

        return $this->validateYears()
        and $this->validateMonths()
        and $this->validateTime()
        and $this->days->validate();
    }

    /**
     *
     */
    public function save()
    {
        $this->saveYears();
        $this->saveMonths();
        $this->saveTime();
        $this->days->save();
    }

    /**
     *
     */
    public function delete()
    {
        ExtraFeeTime::deleteAll(['extraFeeId' => $this->extraFeeId]);
        ExtraFeeMonth::deleteAll(['extraFeeId' => $this->extraFeeId]);
        ExtraFeeYear::deleteAll(['extraFeeId' => $this->extraFeeId]);
        $this->days->delete();
    }

    /**
     * @param $key
     * @param $error
     */
    private function addError($key, $error)
    {
        if (!isset($this->errors[$key])) {
            $this->errors[$key] = [$error];
        } else {
            $this->errors[$key][] = $error;
        }
    }

    /**
     * @param $errorsList
     */
    private function addErrors(Array $errorsList)
    {
        foreach ($errorsList as $key => $error) {
            $this->addError($key, $error);
        }
    }

    /**
     * @return bool
     */
    private function validateYears()
    {
        foreach ($this->dates['years'] as $year) {
            /** @var ExtraFeeYear $yearEntity */
            $yearEntity = new ExtraFeeYear;
            $yearEntity->year = $year;

            if (!$yearEntity->validate()) {
                $this->addErrors($yearEntity->getErrors());
                return false;
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    private function validateMonths()
    {
        foreach ($this->dates['months'] as $month) {
            /** @var ExtraFeeMonth $monthEntity */
            $monthEntity = new ExtraFeeMonth;
            $monthEntity->month = $month;

            if (!$monthEntity->validate()) {
                $this->addErrors($monthEntity->getErrors());
                return false;
            }
        }

        return true;
    }

    /**
     * @return mixed
     */
    private function validateTime()
    {
        $timeEntity = new ExtraFeeTime;
        $timeEntity->timeStart = $this->dates['timeStart'];
        $timeEntity->timeFinish = $this->dates['timeFinish'];

        $isValid = $timeEntity->validate();

        if (!$isValid) {
            $this->addErrors($timeEntity->getErrors());
        }

        return $isValid;
    }

    /**
     * @throws \yii\db\Exception
     */
    private function saveYears()
    {
        $mapToAttributes = function ($year) {
            return ['extraFeeId' => $this->extraFeeId, 'year' => $year];
        };

        $rows = array_map($mapToAttributes, $this->dates['years']);

        \Yii::$app->db->createCommand()->batchInsert(
            ExtraFeeYear::tableName(),
            ['extraFeeId', 'year'],
            $rows
        )->execute();
    }

    /**
     * @throws \yii\db\Exception
     */
    private function saveMonths()
    {
        $mapToAttributes = function ($month) {
            return ['extraFeeId' => $this->extraFeeId, 'month' => $month];
        };

        $rows = array_map($mapToAttributes, $this->dates['months']);

        \Yii::$app->db->createCommand()->batchInsert(
            ExtraFeeMonth::tableName(),
            ['extraFeeId', 'month'],
            $rows
        )->execute();
    }

    /**
     *
     */
    private function saveTime()
    {
        $timeEntity = new ExtraFeeTime;
        $timeEntity->timeStart = $this->dates['timeStart'];
        $timeEntity->timeFinish = $this->dates['timeFinish'];
        $timeEntity->extraFeeId = $this->extraFeeId;

        $timeEntity->save();
    }
}