<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\modules\location\strategies\modellocationpoint\models;


use reinvently\ondemand\core\modules\location\models\A;
use reinvently\ondemand\core\modules\location\models\Point;

/**
 * Class AddressLocationPoint
 * @package reinvently\ondemand\core\modules\location\strategies\modellocationpoint\models
 *
 */
class AddressLocationPoint extends Point
{
    /**
     * @return int
     */
    public static function modelType()
    {
        $classType = static::$classType;
        return $classType::ADDRESS;
    }
}