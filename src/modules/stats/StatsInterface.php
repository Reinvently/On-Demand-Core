<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\stats;


interface StatsInterface
{

    /**
     * Returns the fully qualified name of this class.
     * @return string the fully qualified name of this class.
     *
     * @see yii\base\Object::className()
     */
    public static function className();

    /** @return \stdClass short data for stats */
    public function getStatsObject();

}