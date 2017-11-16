<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */
/**
 * Created by PhpStorm.
 * User: sglushko
 * Date: 28.08.2015
 * Time: 16:09
 */

namespace reinvently\ondemand\core\components\eventmanager;


use yii\base\Component;

abstract class EventManager extends Component
{
    const EVENT_COVER = 'EventManagerEventBase';

    public static function errorEventForRequired(CoverEvent $event)
    {
        throw new NoRequiredEventException($event->name);
    }

    public function init()
    {
        parent::init();

        $this->initRaiserListeners();
        $this->initEventListeners();

        //todo need add lazy load for Listeners
    }

    /**
     * Example:
     *     public function initEventListeners()
     *    {
     *      $this->attache(Test2::RAISE_EVENT_TEST2, Test2::class, 'onEventT1');
     *      $this->attache(Test1::EVENT_TEST1, Test2::class, 'onEventT1');
     *      $this->attache(Test1::EVENT_TEST1, Test2::class, 'onEventT2');
     *      $this->attache(EventManager::EVENT_COVER, StatsComponent::class, 'onEvent');
     *    }
     *
     * @return void
     */
    abstract public function initEventListeners();

    /**
     * Example:
     *     public function initRaiserListeners()
     *    {
     *      $this->attacheRaise(Test2::RAISE_EVENT_TEST2, Test2::class, 'onEvent');
     *    }
     *
     * @return void
     */
    abstract public function initRaiserListeners();

    /**
     * @param string $name
     * @param EventInterface $sender
     * @param CoverEvent $event
     * @throws \Exception
     */
    public function call($name, EventInterface $sender, CoverEvent $event = null)
    {
        if (!$event) {
            $event = new CoverEvent();
        }
        $event->name = $name;
        $event->sender = $sender;
        $coverEvent = new CoverEvent();
        $coverEvent->event = $event;

        $events = $sender::requiredTriggeredEvent();
        $this->addRequiredIfNeed($event, $events);

        try {
            parent::trigger(self::EVENT_COVER, $coverEvent);
            parent::trigger($name, $event);
        } catch (\Exception $e) {
            throw $e;
        //} finally {
            // todo
        }
    }

    /**
     * @param $name
     * @param string $class must implements EventInterface
     * @param $method
     * @param null $data
     * @param bool $append
     */
    public function attacheRaise($name, $class, $method, $data = null, $append = true)
    {
        $this->attache($name, $class, $method, $data, $append, true);
    }

    /**
     * @param $name
     * @param string $class must implements EventInterface
     * @param $method
     * @param null $data
     * @param bool $append
     * @param bool $raise
     */
    public function attache($name, $class, $method, $data = null, $append = true, $raise = false)
    {
        $handler = function (CoverEvent $e) use ($name, $class, $method, $data, $append, $raise) {
            /** @var EventInterface $class */
            $events = $class::requiredOnEvent();
            $this->addRequiredIfNeed($e, $events, $data);
            if ($class::$method($e) && $raise) {
                $e->handled = true;
            }
//            var_export(['handler', $e->name, $e->handled, $e->isRequired, [$class, $method], $e->container]);
        };
        parent::on($name, $handler, $data, false);
    }

    /**
     * @param CoverEvent $e
     * @param array $events
     * @param mixed $data
     */
    protected function addRequiredIfNeed(CoverEvent $e, $events, $data = null)
    {
//        var_export([$e->name, $events]);
        if (!$e->isRequired) {
            if (!empty($events) && array_search($e->name, $events) !== false) {
                $e->isRequired = true;
                parent::on($e->name, [$this, 'errorEventForRequired'], $data, true);
            }
        }
    }

}