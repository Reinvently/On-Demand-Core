<?php namespace reinvently\ondemand\core\vendor\mapsdirections\google;
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

use reinvently\ondemand\core\vendor\mapsdirections\addresses\Address;
use reinvently\ondemand\core\vendor\mapsdirections\google\PlaceKeeper;

class PlaceAddress implements Address
{
    private $placeKeeper;
    
    public function __construct(PlaceKeeper $placeKeeper)
    {
        $this->placeKeeper = $placeKeeper;
    }
    
    public function format()
    {
        return "place_id:" . $this->placeKeeper->getPlaceId();
    }
}