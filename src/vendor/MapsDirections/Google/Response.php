<?php namespace reinvently\ondemand\core\vendor\mapsdirections\google;
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

class Response
{
    const STATUS_OK = "OK";
    const STATUS_NOT_FOUND = "NOT_FOUND";
    const STATUS_ZERO_RESULTS = "ZERO_RESULTS";
    
    private $status;
    private $routes = [];
    private $waypoints = [];

    public function __construct(Array $netResponse)
    {
        $this->status = $netResponse["status"];
        
        if($this->hasResults()) {
            if(isset($netResponse["routes"])) {
                $this->routes = $this->processRoutes($netResponse["routes"]); 
            }
        
            if(isset($netResponse["geocoded_waypoints"])) {
                $this->waypoints = $this->processWaypoints(
                    $netResponse["geocoded_waypoints"]
                ); 
            }
        }
    }
    
    public function completed()
    {
        return ($this->status === self::STATUS_OK 
                or $this->status === self::STATUS_NOT_FOUND 
                or $this->status === self::STATUS_ZERO_RESULTS);
    }
    
    public function hasResults()
    {
         return $this->status === self::STATUS_OK;
    }
    
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return Route[]
     */
    public function getRoutes()
    {
        return $this->routes;
    }
    
    public function getGeocodedWaypoints()
    {
        return $this->waypoints;
    }
    
    private function processWaypoints($geocodedWaypoints)
    {
        $f = function($item) {
            return new GeocodedWaypoint($item);
        };
            
       return array_map($f, $geocodedWaypoints);
    }
    
    private function processRoutes($routes) 
    {
        $f = function($item) {
            return new Route($item);
        };
            
       return array_map($f, $routes);
    }
}