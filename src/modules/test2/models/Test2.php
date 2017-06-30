<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\modules\test2\models;

use reinvently\ondemand\core\components\eventmanager\CoverEvent;
use reinvently\ondemand\core\components\eventmanager\EventInterface;
use yii\base\Model;

class Test2 extends Model implements EventInterface
{
    const RAISE_EVENT_TEST2 = 'Raise_Event_test2';

    public static function onRaiseEvent(CoverEvent $event)
    {
        $event->container->messages[] = 'Raise_Event_test2 handled';
        if (!($event->container instanceof Test2Container)) {
            return false;
        }
        $event->container->messages[] = 'container instanceof Test2Container';

        if ($event->container->p1 != 1) {
            $event->container->messages[] = 'container->p1 != 1';
            $event->container->addError('p1', 'bad p1');
            return true;
        }
        $event->container->messages[] = 'container->p1 == 1';

        return true; //required for required Event if else will be NoRequiredEventException
    }

    public static function onEventT1(CoverEvent $event)
    {
        $event->container->messages[] = 'onEventT1';

        return true; //not required for optional Event

    }

    public static function onEventT2(CoverEvent $event)
    {
        $event->container->messages[] = 'onEventT2';
    }

    public static function requiredTriggeredEvent()
    {
    }

    public static function optionalTriggeredEvent()
    {
    }

    public static function requiredOnEvent()
    {
        return [static::RAISE_EVENT_TEST2];
    }

    public static function optionalOnEvent()
    {
    }
}