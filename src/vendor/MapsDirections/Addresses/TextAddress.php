<?php namespace reinvently\ondemand\core\vendor\mapsdirections\addresses;
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

use app\models\Address as AddressModel;
use reinvently\ondemand\core\vendor\mapsdirections\addresses\Address as DirectionAddresses;

class TextAddress implements DirectionAddresses
{
    private $address;
    
    public function __construct(AddressModel $address)
    {
        $this->address = $address;
    }
        
    public function format()
    {
        return $this->address->format();
    }
}