<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\components\eventmanager;


use reinvently\ondemand\core\components\base\ContainerInterface;
use reinvently\ondemand\core\modules\stats\StatsInterface;
use yii\base\Event;

class CoverEvent extends Event
{
    /** @var CoverEvent */
    public $event;

//    /** @var StatsInterface */
//    public $object;
//
    /** @var ContainerInterface */
    public $container;

    /** @var bool */
    public $isRequired = false;

} 