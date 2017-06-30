<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */
/**
 * Created by PhpStorm.
 * User: sglushko
 * Date: 03.09.2015
 * Time: 16:32
 */

namespace reinvently\ondemand\core\vendor\paymentbraintree;


use reinvently\ondemand\core\components\payment\Payment;
use reinvently\ondemand\core\components\payment\PaymentContainerInterface;
use yii\base\Model;

/**
 * Class BraintreeContainer
 * @package reinvently\ondemand\core\vendor\paymentbraintree
 */
class BraintreeContainer extends Model implements PaymentContainerInterface
{

    /** @var string */
    protected $token;

    /** @var int */
    protected $userId;

    /** @var int */
    protected $orderId;

    /** @var string */
    protected $nonce;

    /** @var string */
    protected $transactionId;

    /** @var int */
    protected $amount;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['userId'], 'required', 'on' => [Payment::RAISE_EVENT_TOKEN]],
            [['nonce'], 'required', 'on' => [Payment::RAISE_EVENT_AUTH]],
            [['orderId'], 'required', 'on' => [Payment::RAISE_EVENT_AUTH]],
            [['transactionId'], 'required', 'on' => [
                Payment::RAISE_EVENT_CANCEL_AUTH,
                Payment::RAISE_EVENT_CANCEL_SALE,
                Payment::RAISE_EVENT_SALE
            ]],
            [['amount'], 'required', 'on' => [
                Payment::RAISE_EVENT_AUTH,
                Payment::RAISE_EVENT_SALE,
                Payment::RAISE_EVENT_CANCEL_SALE
            ]],
        ];
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Order id of core Order
     *
     * @return int
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @param int $orderId
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * @return string
     */
    public function getNonce()
    {
        return $this->nonce;
    }

    /**
     * @param string $nonce
     */
    public function setNonce($nonce)
    {
        $this->nonce = $nonce;
    }

    /**
     * @return string
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * @param string $transactionId
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;
    }

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param int $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }


}