<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\modules\payment\strategies\orderproduct\strategies\braintree\models;


use reinvently\ondemand\core\components\eventmanager\CoverEvent;
use reinvently\ondemand\core\components\eventmanager\EventInterface;
use reinvently\ondemand\core\components\model\CoreModel;
use reinvently\ondemand\core\modules\orderproduct\models\OrderProduct;
use reinvently\ondemand\core\vendor\paymentbraintree\BraintreeContainer;

class Payment extends \reinvently\ondemand\core\modules\payment\strategies\orderproduct\models\Payment implements EventInterface
{

    /**
     * @return void|array Events list for ex. [Test2::RAISE_EVENT_TEST2];
     */
    public static function requiredTriggeredEvent()
    {
        return [
            \reinvently\ondemand\core\components\payment\Payment::RAISE_EVENT_TOKEN,
            \reinvently\ondemand\core\components\payment\Payment::RAISE_EVENT_AUTH,
            \reinvently\ondemand\core\components\payment\Payment::RAISE_EVENT_SALE,
            \reinvently\ondemand\core\components\payment\Payment::RAISE_EVENT_CANCEL_AUTH,
            \reinvently\ondemand\core\components\payment\Payment::RAISE_EVENT_CANCEL_SALE,
        ];
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
    }

    /**
     * @return void|array Events list for ex. [Test2::RAISE_EVENT_TEST2];
     */
    public static function optionalOnEvent()
    {
    }

    /**
     * @param BraintreeContainer $container
     */
    public function makeToken(BraintreeContainer $container)
    {
        $event = new CoverEvent();
        $event->container = $container;

        \Yii::$app->eventManager->call(\reinvently\ondemand\core\components\payment\Payment::RAISE_EVENT_TOKEN, $this, $event);
    }

    public function canPay()
    {
        return true;
    }

    /**
     * @param $nonce
     * @return CoreModel
     */
    public function pay($nonce)
    {

        if (!$this->validate(['order'])) {
            return $this;
        }

        if (!$this->canPay()) {
            return $this;
        }

        $order = $this->order;

        /** @var OrderProduct $class */
        $class = $order->orderProductModelClass;
        $this->price = $class::salesReceipt($this->orderId);
        $this->status = static::STATUS_NEW;

        // начать транзакцию?
        if (!$this->save()) {
            return $this;
        }

        $c = new BraintreeContainer();
        $c->setNonce($nonce);
        $c->setOrderId($this->orderId);
        $c->setAmount($this->price);

        $event = new CoverEvent();
        $event->container = $c;

        \Yii::$app->eventManager->call(\reinvently\ondemand\core\components\payment\Payment::RAISE_EVENT_AUTH, $this, $event);

        if ($c->hasErrors()) {
            $this->saveErrorStatus();
            return $c;
        }

        $this->transactionId = $c->getTransactionId();
        $this->status = static::STATUS_AUTH;

        if (!$this->save()) {
            return $this;
        }

        $event = new CoverEvent();
        $event->container = $c;

        \Yii::$app->eventManager->call(\reinvently\ondemand\core\components\payment\Payment::RAISE_EVENT_SALE, $this, $event);

        if ($c->hasErrors()) {
            $this->saveErrorStatus();
            return $c;
        }

        $this->status = static::STATUS_SALE;

        if (!$this->save()) {
            return $this;
        }

        $order->transition($order::STATUS_PAYMENT_CONFIRMED); // todo сделать транзакцию что бы при ошибке все откатывалось?
        // todo  менять статус через рейз события?

        return $this;

    }

    protected function saveErrorStatus()
    {
        $this->status = static::STATUS_ERROR;
        $this->save(false, ['status']);
    }
}