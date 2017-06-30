<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\settings\models\extrafee\sets;

use reinvently\ondemand\core\modules\settings\models\extrafee\ExtraFee;

/**
 * Class ExtraFeeDaysSet
 * @package app\models\ExtraFee\Sets
 */
class ExtraFeeDaysSet implements ExtraFeeSet
{
    private $daysSet;

    /**
     * ExtraFeeDaysSet constructor.
     * @param ExtraFee $fee
     */
    public function __construct(ExtraFee $fee)
    {
        $this->daysSet = $fee->extraFeeDay
            ? new ExtraFeeMonthDaysSet($fee)
            : new ExtraFeeWeekdaysSet($fee);
    }

    /**
     * @param \DateTime $date
     * @return bool
     */
    public function includes(\DateTime $date)
    {
        return $this->daysSet->includes($date);
    }

    /**
     * @return array
     */
    public function asArray()
    {
        return $this->daysSet->asArray();
    }

    /**
     * @return array
     */
    public function getWeekdays()
    {
        return $this->daysSet instanceof ExtraFeeWeekdaysSet
            ? $this->daysSet->asArray()
            : [];
    }

    /**
     * @return array
     */
    public function getDays()
    {
        return $this->daysSet instanceof ExtraFeeMonthDaysSet
            ? $this->daysSet->asArray()
            : [];
    }

}