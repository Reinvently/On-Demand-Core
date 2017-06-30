<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */
/**
 * Created by PhpStorm.
 * User: sglushko
 * Date: 02.09.2015
 * Time: 15:20
 */

namespace reinvently\ondemand\core\vendor\paymentbraintree;

use reinvently\ondemand\core\components\eventmanager\EventInterface;
use reinvently\ondemand\core\components\payment\exceptions\PaymentException;
use reinvently\ondemand\core\components\payment\Payment;
use reinvently\ondemand\core\components\payment\PaymentInterface;
use reinvently\ondemand\core\modules\user\models\User;
use reinvently\ondemand\core\vendor\paymentbraintree\exceptions\BraintreeException;
use Braintree_Configuration as Braintree_Configuration;
use Yii;
use yii\base\Component;

/**
 * Class Braintree
 * @package reinvently\ondemand\core\vendor\paymentbraintree
 */
class Braintree extends Component implements EventInterface
{

    public function setEnvironment($val)
    {
        Braintree_Configuration::environment($val);
    }

    public function setMerchantId($val)
    {
        Braintree_Configuration::merchantId($val);
    }

    public function setPublicKey($val)
    {
        Braintree_Configuration::publicKey($val);
    }

    public function setPrivateKey($val)
    {
        Braintree_Configuration::privateKey($val);
    }

    /**
     * @return bool
     */
    public static function isProduction()
    {
        return Yii::$app->params['braintree']['environment'] == 'production';
    }

    /**
     * @param $userId
     * @return bool|mixed
     */
    public function getToken($userId)
    {
        return $this->generateToken($userId);
    }

    /**
     * @param BraintreeContainer $c
     * @return bool
     * @throws PaymentException
     */
    public function auth(BraintreeContainer $c)
    {

        $c->setScenario(Payment::RAISE_EVENT_AUTH);
        if (!$c->validate()) {
            return false;
        }

        $arr = [
            'amount' => $c->getAmount(),
//                'options' => [
//                    'submitForSettlement' => true,
//                ],
            'orderId' => $c->getOrderId(),
        ];

        if (!empty($c->getNonce())) {
            $arr['paymentMethodNonce'] = $c->getNonce();
//            } elseif (!empty($paymentMethodToken)) {
//                $arr['paymentMethodToken'] = $paymentMethodToken;
        }

        try {
            /** @var \Braintree_Result_Error|\Braintree_Result_Successful $result */

            $result = \Braintree_Transaction::sale($arr);

            if ($result->success) {
                $c->setTransactionId($result->transaction->id);
                Yii::$app->eventManager->trigger(Payment::EVENT_AUTH, $this);
            } else {
                if ($result->transaction) {
                    $error = $result->message;
                } else {
                    $error = $result->errors->deepAll()[0]->message;
                }
                $c->addError('transactionId', $error);;
            }
        } catch (\Braintree_Exception $e) {
            Yii::warning(
                'Braintree_Exception "' . get_class($e) . '" throwed. '
                . 'Message: ' . $e->getMessage() . '; '
                . 'Attributes: ' . var_export($c->getAttributes(), true));
            $c->addError('transactionId', $e->getMessage());;
        }

        return true;
    }

    /**
     * @param BraintreeContainer $c
     * @return bool
     * @throws PaymentException
     */
    public function cancelAuth(BraintreeContainer $c)
    {
        $c->setScenario(Payment::RAISE_EVENT_CANCEL_AUTH);
        if (!$c->validate()) {
            return false;
        }

        $result = \Braintree_Transaction::void($c->getTransactionId());
        if ($result->success) {
            //todo send params in event
            Yii::$app->eventManager->trigger(Payment::EVENT_CANCEL_AUTH, $this);
            return true;
        }
        throw new BraintreeException($result->errors);
    }

    /**
     * @param BraintreeContainer $c
     * @return bool
     * @throws PaymentException
     */
    public function cancelSale(BraintreeContainer $c)
    {
        $c->setScenario(Payment::RAISE_EVENT_CANCEL_SALE);
        if (!$c->validate()) {
            return false;
        }

        try {
            $transaction = $this->transaction($c->getTransactionId());
            if (!$transaction) {
                throw new BraintreeException('Transaction not found');
            }
            if (in_array($transaction->status, [\Braintree_Transaction::SETTLED, \Braintree_Transaction::SETTLING])) {
                // Refund
                /** @var \Braintree_Result_Error|\Braintree_Result_Successful $result */
                $result = \Braintree_Transaction::refund($c->getTransactionId(), $c->getAmount());
                if ($result->success) {
                    $c->setTransactionId($result->transaction->id);
                    //todo send params in event
                    Yii::$app->eventManager->trigger(Payment::EVENT_CANCEL_SALE, $this);
                    return true;
                } else {
                    if ($result->transaction) {
                        $error = $result->message;
                    } else {
                        $error = $result->errors->deepAll()[0]->message;
                    }
                    throw new BraintreeException($error);
                }
            } else {
                throw new BraintreeException('Unknown transaction status: ' . $transaction->status);
            }
        } catch (\Exception $e) {
            Yii::warning('Exception "' . get_class($e) . '" throwed. Message: ' . $e->getMessage());
            throw new BraintreeException($e->getMessage(), 0, $e);
        }
    }

    /**
     * @param BraintreeContainer $c
     * @return bool
     * @throws PaymentException
     */
    public function sale(BraintreeContainer $c)
    {
        $c->setScenario(Payment::RAISE_EVENT_SALE);
        if (!$c->validate()) {
            return false;
        }

        $result = \Braintree_Transaction::submitForSettlement($c->getTransactionId(), $c->getAmount());

        if ($result->success) {
            Yii::$app->eventManager->trigger(Payment::EVENT_SALE, $this);
            return true;
        }

        if ($result->transaction) {
            $error = $result->message;
        } else {
            $error = $result->errors->deepAll()[0]->message;
        }
        throw new BraintreeException($error);
    }

    /**
     * @param $code
     * @return \Braintree_Transaction
     * @throws PaymentException
     */
    protected function transaction($code)
    {
        try {
            $transaction = \Braintree_Transaction::find($code);
            return $transaction;
        } catch (\Braintree_Exception_NotFound $e) {
            Yii::warning('Exception "' . get_class($e) . '" throwed. Message: ' . $e->getMessage());
            throw new BraintreeException($e->getMessage(), 0, $e);
        }
    }

    /**
     * @param $userId
     * @return bool|mixed
     */
    protected function generateToken($userId)
    {
        try {
            $customer = \Braintree_Customer::find($userId);
        } catch (\Braintree_Exception_NotFound $e) {
            // Register new Customer
            $customer = $this->_registerCustomer($userId);
            if (!$customer) {
                return false;
            }
        }
        $res = \Braintree_ClientToken::generate(array(
            'customerId' => $customer->id,
        ));

        return $res;
    }

    /**
     * @param int $userId
     * @return bool
     */
    private function _registerCustomer($userId)
    {
        //todo подписываться на событие user update и обновлять записть
        //todo сделать под универсального пользователя

        /** @var User $user */
        $user = User::findOne($userId);

        $result = \Braintree_Customer::create(array(
            'id' => $user->id,
            'firstName' => $user->firstName,
            'lastName' => $user->lastName,
            'email' => $user->email,
            'phone' => $user->phone,
        ));
        if ($result->success) {
            return $result->customer;
        }
        return false;
    }

    /**
     * @param $id
     * @param $amount
     * @return bool
     * @throws UserException
     */
    public function submitForSettlement($id, $amount)
    {
        $result = \Braintree_Transaction::submitForSettlement($id, $amount);

        if ($result->success) {
            return true;
        }

        if ($result->transaction) {
            $error = $result->message;
        } else {
            $error = $result->errors->deepAll()[0]->message;
        }
        throw new UserException($error);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function void($id)
    {
        $result = \Braintree_Transaction::void($id);
        if ($result->success) {
            return true;
        }
        throw new UserException($result->errors);
    }

    /**
     * @param int $id
     * @param float|null $amount
     * @return array
     */
    public function refund($id, $amount = null)
    {
        try {
            $transaction = $this->transaction($id);
            if (!$transaction) {
                return ['error' => 'Transaction not found'];
            }
            if ($transaction->status == \Braintree_Transaction::SUBMITTED_FOR_SETTLEMENT) {
                // Void
                $result = \Braintree_Transaction::void($id);
                if ($result->success) {
                    return [
//                        'status' => Order::PAYMENT_STATUS_VOIDED,
                        'message' => 'Transaction successfully voided',
                    ];
                } else {
                    return $result->errors;
                }
            } elseif (in_array($transaction->status, [\Braintree_Transaction::SETTLED, \Braintree_Transaction::SETTLING])) {
                // Refund

                /** @var \Braintree_Result_Error|\Braintree_Result_Successful $result */
                $result = \Braintree_Transaction::refund($id, $amount);
                if ($result->success) {
                    return [
//                        'status' => Order::PAYMENT_STATUS_REFUNDED,
                        'message' => 'Transaction successfully refunded',
                        'transactionId' => $result->transaction->id
                    ];
                } else {
                    if ($result->transaction) {
                        $error = $result->message;
                    } else {
                        $error = $result->errors->deepAll()[0]->message;
                    }
                    return ['error' => $error];
                }
            } else {
                return ['error' => 'Unknown transaction status: ' . $transaction->status];
            }


        } catch (\Exception $e) {
            return ['error' => 'Exception "' . get_class($e) . '" throwed.'];
        }
    }

    /**
     * @param $id
     * @param bool $registerNew
     * @return bool|\Braintree_Customer
     */
    public function getCustomer($id, $registerNew = false)
    {

        $customer = Yii::$app->cache->get(self::CACHE_CUSTOMER_KEY . $id);
        if ($customer) {
            return $customer;
        }

        try {
            $customer = \Braintree_Customer::find($id);
        } catch (\Braintree_Exception_NotFound $e) {
            if ($registerNew) {
                // Register new Customer
                $customer = $this->_registerCustomer(User::findOne($id));
            } else {
                return false;
            }
        }
        if (!$customer) {
            return false;
        }

        Yii::$app->cache->set(self::CACHE_CUSTOMER_KEY . $id, $customer);
        return $customer;
    }

    /**
     * @param int $id
     * @param bool $reset
     * @return bool
     */
    public function clearCustomerCache($id, $reset = true)
    {
        $res = Yii::$app->cache->delete(self::CACHE_CUSTOMER_KEY . $id);

        if ($reset) {
            $this->getCustomer($id, true);
        }

        return $res;
    }

    public function deleteCustomer($id)
    {
        $customer = $this->getCustomer($id);
        if ($customer) {
            \Braintree_Customer::delete($id);
            $this->clearCustomerCache($id, false);
            return true;
        }
        return false;
    }

    public function addPayment($userId, $paymentMethodNonce, $isDefault = false)
    {
        $customer = $this->getCustomer($userId);
        if (!$customer) {
            // Register new Customer
            $customer = $this->_registerCustomer(User::findOne($userId));
            if (!$customer) {
                return [
                    'success' => false,
                    'errors' => 'Cannot register customer',
                ];
            }
        }

        $options = [
            'failOnDuplicatePaymentMethod' => true,
        ];
        if ($isDefault) {
            $options['makeDefault'] = true;
        }

//        var_export([
//            'customerId' => $customer->id,
//            'paymentMethodNonce' => $paymentMethodNonce,
//            'options' => $options
//        ]);
        $result = \Braintree_PaymentMethod::create(array(
            'customerId' => $customer->id,
            'paymentMethodNonce' => $paymentMethodNonce,
            'options' => $options
        ));
//        var_export($result);

        if ($result->success) {
            $this->clearCustomerCache($userId);
            return [
                'success' => true,
                'paymentMethod' => $this->getJson($result->paymentMethod),
            ];

        } else {
            $errors = '';
            foreach ($result->errors->deepAll() as $error) {
                $errors .= $error->message . ' ';
            }
            return [
                'success' => false,
                'errors' => $errors,
            ];
        }

    }

    public function updatePayment($token, $isDefault, $userId)
    {
        $options = [];

        if ($isDefault) {
            $options['makeDefault'] = true;
        }

        try {
            $result = \Braintree_PaymentMethod::update($token, ['options' => $options]);
            if ($result->success) {
                $this->clearCustomerCache($userId);
                return [
                    'success' => true,
//                    'paymentMethod' => $this->getJson($result->paymentMethod),
                ];

            } else {
                $errors = '';
                foreach ($result->errors->deepAll() as $error) {
                    $errors .= $error->message . ' ';
                }
                return [
                    'success' => false,
                    'errors' => $errors,
                ];
            }
        } catch (\Braintree_Exception_NotFound $e) {
            return [
                'success' => false,
                'errors' => 'Token not found',
            ];
        }
    }


    public function deletePayment($token, $userId)
    {
        try {
            $result = \Braintree_PaymentMethod::delete($token);
            $this->clearCustomerCache($userId);
            return [
                'success' => $result,
            ];
        } catch (\Braintree_Exception_NotFound $e) {
            return [
                'success' => false,
                'errors' => 'Token not found',
            ];
        }
    }


    public function getCardJson($c)
    {
        /** @var \Braintree_CreditCard $c */
        return [
            'isDefault' => $c->default,
            'paymentToken' => $c->token,
            'paymentMethodInfo' => [
                'paymentTypeName' => $c->cardType,
                'last4' => $c->last4,
                'type' => self::PAYMENT_METHOD_TYPE_CREDIT_CARD,
                'imageUrl' => $c->imageUrl,
            ]
        ];
    }

    public function getPaypalJson($c)
    {
        /** @var \Braintree_PayPalAccount $c */
        return [
            'isDefault' => $c->default,
            'paymentToken' => $c->token,
            'paymentMethodInfo' => [
                'paymentTypeName' => 'PayPal',
                'email' => $c->email,
                'type' => self::PAYMENT_METHOD_TYPE_PAY_PAL,
                'imageUrl' => $c->imageUrl,
            ]
        ];
    }

    public function getJson($paymentMethod)
    {
        $res = false;

        if ($paymentMethod instanceof \Braintree_CreditCard) {
            $res = $this->getCardJson($paymentMethod);
            $res['type'] = self::PAYMENT_METHOD_TYPE_CREDIT_CARD;
        } elseif ($paymentMethod instanceof \Braintree_PayPalAccount) {
            $res = $this->getPayPalJson($paymentMethod);
            $res['type'] = self::PAYMENT_METHOD_TYPE_PAY_PAL;
        }

        return $res;
    }


    public function findTransaction($transactionId)
    {
        try {
            return \Braintree_Transaction::find($transactionId);
        } catch (\Braintree_Exception_NotFound $ex) {
            return false;
        }
    }

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
    }

    /**
     * @return void|array Events list for ex. [Test2::RAISE_EVENT_TEST2];
     */
    public static function optionalOnEvent()
    {
    }
}