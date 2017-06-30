<?php namespace reinvently\ondemand\core\vendor\mapsdirections\google\Waypoints;
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

use reinvently\ondemand\core\vendor\mapsdirections\addresses\Address as DirectionsAddress;

class AddressWaypoint implements Waypoint
{
    private $via;
    private $address;
    
    public function __construct(DirectionsAddress $address, $via = false)
    {
        $this->address = $address;
        $this->via = $via;
    }
    
    public function viaIt()
    {
        $this->via = true;
    }

    public function format()
    {
        return $this->via 
            ? "via:" . $this->address->format() 
            : $this->address->format();
    }
}