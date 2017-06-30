<?php namespace reinvently\ondemand\core\vendor\mapsdirections\google;
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

class Types
{
    const MODE_DRIVING = "driving";
    const MODE_WALKING = "walking";
    const MODE_BICYCLING = "bicycling";
    const MODE_TRANSIT = "transit";
    
    const UNIT_METRIC = "metric";
    const UNIT_IMPERIAL = "imperial";
    
    static private $modes = [
        self::MODE_DRIVING,
        self::MODE_WALKING,
        self::MODE_BICYCLING,
        self::MODE_TRANSIT
    ];
    
    static private $units = [
        self::UNIT_METRIC,
        self::UNIT_IMPERIAL
    ];
    
    static public function getModesList()
    {
        return self::$modes;
    }
    
    static public function getUnitsList()
    {
        return self::$units;
    }
    
    static public function checkMode($mode)
    {
        return in_array($mode, self::getModesList());
    }
    
    static public function checkUnit($unit)
    {
        return in_array($unit, self::getUnitsList());
    }
}