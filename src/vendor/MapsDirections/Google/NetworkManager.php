<?php namespace reinvently\ondemand\core\vendor\mapsdirections\google;
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

use reinvently\ondemand\core\vendor\mapsdirections\google\Response;
use reinvently\ondemand\core\vendor\mapsdirections\exceptions\LogicException;
use reinvently\ondemand\core\vendor\mapsdirections\exceptions\TransportException;

class NetworkManager
{
    const URL = "https://maps.googleapis.com/maps/api/directions/json";
    
    private $ch;

    public function __construct()
    {
        $this->ch = $this->prepare(curl_init());
    }
    
    public function __destruct()
    {
        curl_close($this->ch);
    }
    
    public function request(RequestParams $params)
    {
        $url = self::URL . "?" . http_build_query($params->toArray());
        curl_setopt($this->ch, CURLOPT_URL, $url); 
        
        $res = curl_exec($this->ch);
        
        if(!$res) {
            throw new TransportException(curl_error($this->ch));
        }
        
        $jsonData = json_decode($res, 1);
        
        if(!$jsonData) {
            throw new LogicException("Invalid JSON data", json_last_error());
        }
        
        return new Response($jsonData);
    }
    
    private function prepare($ch)
    {
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false); 
        
        return $ch;
    }
}