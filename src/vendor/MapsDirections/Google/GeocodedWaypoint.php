<?php namespace reinvently\ondemand\core\vendor\mapsdirections\google;
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

class GeocodedWaypoint implements PlaceKeeper
{
    const STATUS_OK = "OK";
    
    private $status;
    private $types;
    private $placeId;
    
    public function __construct(Array $params)
    {
        $this->types = $params["types"];
        $this->placeId = $params["place_id"];
        $this->status = $params["geocoder_status"];
    }
    
    public function getPlaceId()
    {
        return $this->placeId;
    }
    
    public function getTypes()
    {
        return $this->types;
    }
    
    public function processed()
    {
        return $this->status === self::STATUS_OK;
    }
}