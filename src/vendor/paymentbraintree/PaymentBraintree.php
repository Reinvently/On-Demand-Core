<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\vendor\paymentbraintree;


use reinvently\ondemand\core\components\eventmanager\CoverEvent;
use reinvently\ondemand\core\components\payment\Payment;

class PaymentBraintree extends Payment
{
    /**
     * @return void|array Events list for ex. [Test2::RAISE_EVENT_TEST2];
     */
    public static function requiredTriggeredEvent()
    {
    }

    /**
     * @return void|array Events list for ex. [Test2::RAISE_EVENT_TEST2];
     */
    public static function optionalTriggeredEvent()
    {
    }

    /**
     * @return void|array Events list for ex. [Test2::RAISE_EVENT_TEST2];
     */
    public static function requiredOnEvent()
    {
        return [
            Payment::RAISE_EVENT_TOKEN,
            Payment::RAISE_EVENT_AUTH,
            Payment::RAISE_EVENT_SALE,
            Payment::RAISE_EVENT_CANCEL_AUTH,
            Payment::RAISE_EVENT_CANCEL_SALE,
        ];
    }

    /**
     * @return void|array Events list for ex. [Test2::RAISE_EVENT_TEST2];
     */
    public static function optionalOnEvent()
    {
    }

    /**
     * @param CoverEvent $event
     * @return bool
     */
    public static function onToken(CoverEvent $event)
    {
        if (!($event->container instanceof BraintreeContainer)) {
            return false;
        }

        $event->container->setScenario(Payment::RAISE_EVENT_TOKEN);
        if (!$event->container->validate()) {
            return true;
        }

        $token = \Yii::$app->braintree->getToken($event->container->getUserId());

        if (!$token) {
            throw new \LogicException('Token must be');
        }

        $event->container->setToken($token);
        return true;

    }

    /**
     * @param CoverEvent $event
     * @return bool
     * @throws exceptions\BraintreeException
     */
    public static function onAuth(CoverEvent $event)
    {
        if (!($event->container instanceof BraintreeContainer)) {
            return false;
        }

        $event->container->setScenario(Payment::RAISE_EVENT_AUTH);
        if (!$event->container->validate()) {
            return true;
        }

        return \Yii::$app->braintree->auth($event->container);
    }

    /**
     * @param CoverEvent $event
     * @return bool
     * @throws exceptions\BraintreeException
     */
    public static function onSale(CoverEvent $event)
    {
        if (!($event->container instanceof BraintreeContainer)) {
            return false;
        }

        $event->container->setScenario(Payment::RAISE_EVENT_SALE);
        if (!$event->container->validate()) {
            return true;
        }

        return \Yii::$app->braintree->sale($event->container);
    }

//    public function init()
//    {
//        \Yii::$app->eventManager->on(static::RAISE_EVENT_AUTH, function (CoverEvent $e){
//            if ($e->container instanceof BraintreeContainer) {
//                \Yii::$app->payment->auth($e->container);
//                $e->handled = true;
//            }
//        });
//    }

}