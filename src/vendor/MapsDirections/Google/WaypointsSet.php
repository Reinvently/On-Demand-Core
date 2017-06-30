<?php namespace reinvently\ondemand\core\vendor\mapsdirections\google;
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

use reinvently\ondemand\core\vendor\mapsdirections\google\Waypoints\Waypoint as Waypoint;

class WaypointsSet
{
    private $waypoints = [];
    private $optimize = false;
    
    public function __construct(Array $waypoints = [])
    {
        $this->setWaypoints($waypoints);
    }
    
    public function setWaypoints(Array $waypoints) 
    {
        foreach($waypoints as $waypoint) {
            $this->addWaypoint($waypoint);
        }
    }
    
    public function addWaypoint(Waypoint $waypoint)
    {
        $this->waypoints[] = $waypoint;
    }
    
    public function viaWaypoint(Waypoint $waypoint)
    {
        $waypoint->viaIt();
        $this->waypoints[] = $waypoint;
    }
    
    public function optimize()
    {
        $this->optimize = true;
    }
    
    public function format()
    {
        $map = function(Waypoint $waypoint) {
            return $waypoint->format();
        };
        
        $waypoints = implode("|", array_map($map,  $this->waypoints));
        return $this->optimize ? "optimize:true|" . $waypoints : $waypoints;
    }
}