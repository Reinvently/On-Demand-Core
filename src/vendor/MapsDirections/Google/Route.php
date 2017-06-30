<?php namespace reinvently\ondemand\core\vendor\mapsdirections\google;
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

class Route
{
    private $legs;
    private $fare;
    private $summary;
    private $copyrights;
    private $waypointOrder;
    private $warnings = [];
   
    public function __construct(Array $params)
    {
        $this->summary = $params["summary"];
        $this->legs = $this->processLegs($params["legs"]);
        
        if(isset($params["fare"])) {
            $this->fare = $params["fare"];
        }
        
        if(isset($params["copyrights"])) {
            $this->copyrights = $params["copyrights"];
        }
        
        if(isset($params["waypoint_order"])) {
            $this->waypointOrder = $params["waypoint_order"];
        }
        
        if(isset($params["warnings"]) and $params["warnings"]) {
            $this->warnings = $params["warnings"];
        }
    }
    
    public function getLegs()
    {
        return $this->legs;
    }
    
    public function getWaypointOrder()
    {
        return $this->waypointOrder;
    }
    
    public function getFare()
    {
        return $this->fare;
    }
    
    public function hasWarnings()
    {
        return !empty($this->warnings);
    }
    
    public function getWarnings()
    {
        return $this->warnings;
    }
    
    public function getCopyrights()
    {
        return $this->copyrights;
    }
    
    public function getSummary()
    {
        return $this->summary;
    }
    
    public function getTotalDistance()
    {
        $collect = function($acc, Leg $el) {
            return $acc + $el->getDistance();
        };
        
        return array_reduce($this->legs, $collect, 0);
    }
    
    
    private function processLegs($legs) 
    {
        $f = function($item) {
            return new Leg($item);
        };
            
       return array_map($f, $legs);
    }
}