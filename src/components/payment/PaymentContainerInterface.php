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
 * Time: 16:34
 */

namespace reinvently\ondemand\core\components\payment;


use reinvently\ondemand\core\components\base\ContainerInterface;

interface PaymentContainerInterface extends ContainerInterface
{
    /**
     * Order id of core Order
     *
     * @return int
     */
    public function getOrderId();

    /**
     * @param int $orderId
     */
    public function setOrderId($orderId);

    /**
     * @return string
     */
    public function getNonce();

    /**
     * @param string $nonce
     */
    public function setNonce($nonce);

    /**
     * @return string
     */
    public function getTransactionId();

    /**
     * @param string $transactionId
     */
    public function setTransactionId($transactionId);

    /**
     * @return array the validation rules.
     */
    public function rules();

    /**
     * Performs the data validation.
     *
     * @return bool
     */
    public function validate();

    /**
     * @param mixed $value
     */
    public function setScenario($value);

    /**
     * @return int
     */
    public function getAmount();

    /**
     * @param int $amount
     */
    public function setAmount($amount);

} 