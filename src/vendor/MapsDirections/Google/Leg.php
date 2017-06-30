<?php namespace reinvently\ondemand\core\vendor\mapsdirections\google;
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

class Leg
{
    private $params;
    
    public function __construct($params)
    {
        $this->params = $params;
    }
    
    public function getDistance()
    {
        return $this->params["distance"]["value"];
    }
    
    
    public function getDistanceDescription()
    {
        return $this->params["distance"]["text"];
    }
}