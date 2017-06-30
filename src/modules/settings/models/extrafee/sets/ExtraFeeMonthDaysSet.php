<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\settings\models\extrafee\sets;

use reinvently\ondemand\core\modules\settings\models\extrafee\ExtraFee;

/**
 * Class ExtraFeeMonthDaysSet
 * @package reinvently\ondemand\core\modules\settings\models\extrafee\sets
 */
class ExtraFeeMonthDaysSet implements ExtraFeeSet
{
    private $days;

    /**
     * ExtraFeeMonthDaysSet constructor.
     * @param ExtraFee $fee
     */
    public function __construct(ExtraFee $fee)
    {
        $this->days = array_map(function ($el) {
            return $el->day;
        }, $fee->extraFeeDay);
    }

    /**
     * @param \DateTime $date
     * @return bool
     */
    public function includes(\DateTime $date)
    {
        return in_array($date->format("j"), $this->days);
    }

    /**
     * @return array
     */
    public function asArray()
    {
        return $this->days;
    }
}