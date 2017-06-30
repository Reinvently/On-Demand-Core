<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\components\helpers;

/**
 * Class DateHelper
 * @package reinvently\ondemand\core\components\helpers
 */
class DateHelper
{
    /**
     * Generate date range
     *
     * @param $first
     * @param $last
     * @param string $step
     * @param string $output_format
     * @return array
     */
    public static function dateRange($first, $last, $step = '+1 day', $output_format = 'Y-m-d' ) {

        $dates = [];
        $current = strtotime($first);
        $last = strtotime($last);

        while( $current <= $last ) {
            $dates[] = date($output_format, $current);
            $current = strtotime($step, $current);
        }

        return $dates;
    }

    /**
     * @return array
     */
    public static function weekList()
    {
        $weekPeriod = new \DatePeriod(
            new \DateTime("Sunday this week"),
            new \DateInterval("P1D"),
            new \DateTime("Sunday next week")
        );

        $weekList = [];
        foreach($weekPeriod as $w){
            $weekList[(int)$w->format("w")] = $w->format("l");
        }
        return $weekList;
    }
}