<?php namespace reinvently\ondemand\core\vendor\mapsdirections\google\Waypoints;
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

interface Waypoint
{
    public function format();
    
    public function viaIt(); 
}