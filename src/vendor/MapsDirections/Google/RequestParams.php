<?php namespace reinvently\ondemand\core\vendor\mapsdirections\google;
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

use reinvently\ondemand\core\vendor\mapsdirections\addresses\Address;

class RequestParams extends \yii\base\Component
{
    private $apiKey;
    
    private $mode;
    private $units;
    private $origin;
    private $region;
    private $waypoints;
    private $destination;
    private $alternatives = false;
    
    
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }
    
    public function setMode($mode)
    {
        assert(Types::checkMode($mode), "Mode type verification");
        $this->mode = $mode;
    }
    
    public function setRegion($region)
    {
        $this->region = $region;
    }
    
    public function setUnits($units)
    {
        assert(Types::checkUnit($units), "Units type verification");
        $this->units = $units;
    }
    
    public function setWaypoints(WaypointsSet $waypoints)
    {
        $this->waypoints = $waypoints;
    }
    
    public function needAlternatives()
    {
        $this->alternatives = true;
    }
    
    public function setOrigin(Address $origin)
    {
        $this->origin = $origin;
    }
    
    public function setDestination(Address $destination)
    {
        $this->destination = $destination;
    }
    
    public function toArray()
    {
        $params["origin"] = $this->origin->format();
        $params["destination"] = $this->destination->format();
        
        if($this->apiKey) {
            $params["key"] = $this->apiKey;
        }

        if($this->mode) {
            $params["mode"] = $this->mode;
        }
        
        if($this->region) {
            $params["region"] = $this->region;
        }
        
        if($this->units) {
            $params["units"] = $this->units;
        }
        
        if($this->alternatives) {
            $params["alternatives"] = $this->alternatives;
        }
        
        if($this->waypoints) {
            $params["waypoints"] = $this->waypoints->format();
        }
        
        return $params;
    }
}