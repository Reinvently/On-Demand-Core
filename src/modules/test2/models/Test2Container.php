<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\modules\test2\models;


use reinvently\ondemand\core\components\base\ContainerInterface;
use reinvently\ondemand\core\components\base\ErrorManagerTrait;
use reinvently\ondemand\core\components\transport\ApiInterface;
use reinvently\ondemand\core\components\transport\ApiTransportTrait;

class Test2Container implements ContainerInterface, ApiInterface
{
    use ErrorManagerTrait, ApiTransportTrait;

    public $p1;

    /**
     * @return array
     */
    public function getItemForApi()
    {
        return (array) $this;
    }

    /**
     * @return array
     */
    public function getItemShortForApi()
    {
        $this->getItemForApi();
    }
}