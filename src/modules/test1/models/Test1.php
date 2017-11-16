<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\modules\test1\models;


use reinvently\ondemand\core\components\eventmanager\CoverEvent;
use reinvently\ondemand\core\components\eventmanager\EventInterface;
use reinvently\ondemand\core\modules\test2\models\Test2;
use reinvently\ondemand\core\modules\test2\models\Test2Container;
use yii\base\Model;

class Test1 extends Model implements EventInterface
{
    const EVENT_TEST1 = 'Event_test1';

    public function raise()
    {
        $container = new Test2Container(); // for check example
        $container->messages = []; // for check example

        $event = new CoverEvent();
        $event->container = $container; // for check example
        $container->messages[] = 'trigger EVENT_TEST1'; // for check example
        \Yii::$app->eventManager->call(static::EVENT_TEST1, $this, $event);

        $event = new CoverEvent();
        $event->container = $container; // for check example
        $event->container->p1 = 1;
        try {
            $container->messages[] = 'trigger 1 RAISE_EVENT_TEST2'; // for check example
            \Yii::$app->eventManager->call(Test2::RAISE_EVENT_TEST2, $this, $event);
        } catch (\NoRequiredEventException $e) {
            throw $e;
        }

        $event = new CoverEvent();
        $event->container = $container; // for check example
        $event->container->p1 = 0;
        try {
            $container->messages[] = 'trigger 2 RAISE_EVENT_TEST2'; // for check example
            \Yii::$app->eventManager->call(Test2::RAISE_EVENT_TEST2, $this, $event);
        } catch (\NoRequiredEventException $e) {
            throw $e;
        }

        return $container;
    }

    public function raiseException()
    {
        $container = new Test2Container(); // for check example
        $container->messages = []; // for check example

        $event = new CoverEvent();
        $event->container = $container;
        $event->container->p1 = 0;

        try {
            \Yii::$app->eventManager->call(Test2::RAISE_EVENT_TEST2, $this, $event);
        } catch (\NoRequiredEventException $e) {
            var_export($event->container->getErrors());
            throw $e;
        }

        return $container;
    }

    public static function requiredTriggeredEvent()
    {
        return [Test2::RAISE_EVENT_TEST2];
    }

    public static function optionalTriggeredEvent()
    {
        return [static::EVENT_TEST1];
    }

    public static function requiredOnEvent()
    {
    }

    public static function optionalOnEvent()
    {
    }
}