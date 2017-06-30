<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\settings\models\extrafee\sets;

use reinvently\ondemand\core\modules\settings\models\extrafee\ExtraFee;

/**
 * Class ExtraFeeWeekdaysSet
 * @package reinvently\ondemand\core\modules\settings\models\extrafee\sets
 */
class ExtraFeeWeekdaysSet implements ExtraFeeSet
{
    private $weekdays;

    /**
     * ExtraFeeWeekdaysSet constructor.
     * @param ExtraFee $fee
     */
    public function __construct(ExtraFee $fee)
    {
        $this->weekdays = array_map(function($el) {
            return $el->weekday;
        }, $fee->extraFeeWeekday);
    }

    /**
     * @param \DateTime $date
     * @return bool
     */
    public function includes(\DateTime $date)
    {
        return in_array($date->format("N"), $this->weekdays);
    }

    /**
     * @return array
     */
    public function asArray()
    {
        return $this->weekdays;
    }
}