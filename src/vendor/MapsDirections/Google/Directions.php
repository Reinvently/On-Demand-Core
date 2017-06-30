<?php namespace reinvently\ondemand\core\vendor\mapsdirections\google;
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

use reinvently\ondemand\core\vendor\mapsdirections\addresses\Address;
use reinvently\ondemand\core\vendor\mapsdirections\exceptions\LogicException;

class Directions extends \yii\base\Component
{
    private $params;
    private $network;

    /**
     *
     */
    public function init()
    {
        parent::init();
        $this->network = new NetworkManager;
    }

    /**
     * @param RequestParams|array $params
     */
    public function setParams($params)
    {
        if (is_array($params)) {
            $params = new RequestParams($params);
        }
        $this->params = $params;
    }

    public function setRouteSelector(\Closure $routeSelector)
    {
        $this->routeSelector = $routeSelector;
    }

    public function request(Address $origin, Address $destination)
    {
        $this->params->setOrigin($origin);
        $this->params->setDestination($destination);
        $response = $this->network->request($this->params);

        if (!$response->completed()) {
            throw new LogicException($response->getStatus());
        }

        return $response;
    }
}