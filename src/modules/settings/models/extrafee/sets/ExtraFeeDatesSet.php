<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\settings\models\extrafee\sets;
use reinvently\ondemand\core\modules\settings\models\extrafee\ExtraFee;

/**
 * Class ExtraFeeDatesSet
 * @package reinvently\ondemand\core\modules\settings\models\extrafee\sets
 */
class ExtraFeeDatesSet implements ExtraFeeSet
{
    private $years;
    private $months;
    private $days;
    private $timeStart;
    private $timeEnd;

    /**
     * ExtraFeeDatesSet constructor.
     * @param ExtraFee $fee
     */
    public function __construct(ExtraFee $fee)
    {
        $this->years = array_map(function ($el) {
            return $el->year;
        }, $fee->extraFeeYear);

        $this->months = array_map(function ($el) {
            return $el->month;
        }, $fee->extraFeeMonth);

        $this->days = new ExtraFeeDaysSet($fee);

        $this->timeStart = $fee->extraFeeTime->timeStart;
        $this->timeFinish = $fee->extraFeeTime->timeFinish;
    }


    public function includes(\DateTime $date)
    {
        return in_array($date->format("Y"), $this->years)
        and in_array($date->format("n"), $this->months)
        and $this->days->includes($date)
        and $this->timeIncludes($date);
    }


    public function asArray()
    {
        return array(
            "years" => $this->years,
            "months" => $this->months,
            "weekdays" => $this->days->getWeekdays(),
            "days" => $this->days->getDays(),
            "timeStart" => $this->timeStart,
            "timeEnd" => $this->timeEnd
        );
    }


    private function timeIncludes($date)
    {
        $timeStart = new \DateTime($this->timeStart);
        $timeEnd = new \DateTime($this->timeEnd);

        if ($timeStart == $timeEnd) {
            return true;
        }

        return ($timeStart > $timeEnd)
            ? !($date > $timeEnd and $date < $timeStart)
            : ($date >= $timeStart and $date <= $timeEnd);
    }
}