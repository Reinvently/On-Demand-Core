<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\settings\models\extrafee\sets;

use reinvently\ondemand\core\modules\settings\models\extrafee\ExtraFee;

/**
 * Interface ExtraFeeSet
 * @package reinvently\ondemand\core\modules\settings\models\extrafee\sets
 */
interface ExtraFeeSet
{
    /**
     * ExtraFeeSet constructor.
     * @param ExtraFee $fee
     */
    public function __construct(ExtraFee $fee);

    /**
     * @param \DateTime $date
     * @return mixed
     */
    public function includes(\DateTime $date);

    /**
     * @return mixed
     */
    public function asArray();
}