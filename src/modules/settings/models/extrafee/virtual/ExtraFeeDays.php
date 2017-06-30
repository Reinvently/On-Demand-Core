<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\settings\models\extrafee\virtual;
use reinvently\ondemand\core\modules\settings\models\extrafee\ExtraFeeDay;
use reinvently\ondemand\core\modules\settings\models\extrafee\ExtraFeeWeekday;

/**
 * Class ExtraFeeDays
 * @package reinvently\ondemand\core\modules\settings\models\extrafee\virtual
 */
class ExtraFeeDays implements ExtraFeeVirtualModel
{
    private $days;
    private $extraFeeId;
    private $errors = [];

    /**
     * @param array $dates
     */
    public function setDates(Array $dates)
    {
        if (isset($dates["days"]) and !empty($dates["days"])) {
            $this->days = new ExtraFeeMonthdays($dates["days"]);
        } elseif (isset($dates["weekdays"]) and !empty($dates["weekdays"])) {
            $this->days = new ExtraFeeWeekdays($dates["weekdays"]);
        }
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

        if ($this->days) {
            $this->days->setExtraFeeId($id);
        }
    }

    /**
     * @return bool
     */
    public function validate()
    {
        if (!$this->days) {
            $this->addError("days", "Can not be empty when weekday empty");
            return false;
        }

        return $this->validateDays();
    }

    /**
     *
     */
    public function save()
    {
        $this->days->save();
    }

    /**
     *
     */
    public function delete()
    {
        ExtraFeeDay::deleteAll(["extraFeeId" => $this->extraFeeId]);
        ExtraFeeWeekday::deleteAll(["extraFeeId" => $this->extraFeeId]);
    }

    /**
     * @return bool
     */
    private function validateDays()
    {
        if (!$this->days->validate()) {
            $this->addErrors($this->days->getErrors());
            return false;
        }

        return true;
    }

    /**
     * @param $errorsList
     */
    private function addErrors($errorsList)
    {
        foreach ($errorsList as $key => $error) {
            $this->addError($key, $error);
        }
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
}
