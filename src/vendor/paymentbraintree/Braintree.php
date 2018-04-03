<?php
/**
 * @copyright Reinvently (c) 2018
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

use app\exceptions\PaymentDuplicateException;
use Braintree\ClientToken;
use Braintree\Configuration;
use Braintree\CreditCard;
use Braintree\Customer;
use Braintree\Exception\NotFound;
use Braintree\MerchantAccount;
use Braintree\PaymentMethod;
use Braintree\PayPalAccount;
use Braintree\Transaction;
use reinvently\ondemand\core\exceptions\UserException;
use reinvently\ondemand\core\components\loggers\models\ExceptionLog;
use reinvently\ondemand\core\modules\user\models\User;

class Braintree
{
    const CACHE_TRANSACTION = 'BraintreeTransaction';
    const CACHE_CUSTOMER_KEY = 'BraintreeCustomer';
    const CACHE_TRANSACTIONS_CREDIT_CARD = 'TransactionsCreditCard';

    const CACHE_TRANSACTION_DURATION = 3600;

    const PAYMENT_METHOD_TYPE_PAY_PAL = 'PayPal';
    const PAYMENT_METHOD_TYPE_CREDIT_CARD = 'CreditCard';

    public $result;

    public function __construct()
    {
        $this->configuration();
    }

    public function configuration()
    {
        $config = \Yii::$app->params['braintree'];
        Configuration::environment($config['environment']);
        Configuration::merchantId($config['merchantId']);
        Configuration::publicKey($config['publicKey']);
        Configuration::privateKey($config['privateKey']);
    }

    /**
     * Get array of destination types for creating merchant account
     * https://developers.braintreepayments.com/reference/request/merchant-account/create/php
     *
     * @return array
     */
    public static function getDestinations()
    {
        return [
            MerchantAccount::FUNDING_DESTINATION_BANK => 'Bank',
            MerchantAccount::FUNDING_DESTINATION_EMAIL => 'Email',
            MerchantAccount::FUNDING_DESTINATION_MOBILE_PHONE => 'Phone',
        ];
    }

    /**
     * @param $userId
     * @return string
     * @throws UserException
     */
    public function generateToken($userId)
    {
        try {
            $customer = Customer::find($userId);
        } catch (NotFound $e) {
            $customer = $this->registerCustomer(User::findOne($userId));
            if (!$customer) {
                throw new UserException('Can not register customer');
            }
        }
        /** @var string $token */
        $token = ClientToken::generate(array(
            'customerId' => $customer->id,
        ));
        return $token;
    }

    /**
     * @param User $user
     * @return mixed|bool
     */
    protected function registerCustomer(User $user)
    {
        try {
            $result = Customer::create(array(
                'id' => $user->id,
                'firstName' => $user->firstName,
                'lastName' => $user->lastName,
                'email' => $user->email,
                'phone' => $user->phone,
            ));
        } catch (\Exception $e) {
            ExceptionLog::saveException($e);
            return false;
        }

        if ($result->success) {
            return $result->customer;
        }
        return false;
    }

    /**
     * @param string|float $amount
     * @param int $orderId
     * @param string $paymentMethodToken
     * @param string $nonce
     * @param bool $submitForSettlement
     * @param string $merchantAccountId
     * @param float $serviceFeeAmount
     * @return bool
     * @throws UserException
     */
    public function sale($amount, $orderId, $paymentMethodToken = null, $nonce = null, $submitForSettlement = false, $merchantAccountId = null, $serviceFeeAmount = null)
    {
        try {
            /** @var \Braintree\Result\Error|\Braintree\Result\Successful $result */

            $arr = [
                'amount' => $amount,
                'options' => [
                    'submitForSettlement' => $submitForSettlement,
                ],
                'orderId' => $orderId,
            ];

            if (!empty($nonce)) {
                $arr['paymentMethodNonce'] = $nonce;
            } elseif (!empty($paymentMethodToken)) {
                $arr['paymentMethodToken'] = $paymentMethodToken;
            } else {
                throw new \InvalidArgumentException('Must be set nonce or paymentMethodToken');
            }

            if (!empty($merchantAccountId)) {
                $arr['merchantAccountId'] = $merchantAccountId;
            }

            if ($serviceFeeAmount !== null) {
                $arr['serviceFeeAmount'] = $serviceFeeAmount;
                $arr['options']['holdInEscrow'] = true;
            }

            $result = Transaction::sale($arr);

            $this->result = $result;

            if ($result->success) {
                return true;
            } else {
                if ($result->transaction) {
                    $error = $result->message;
                } else {
                    $error = $result->errors->deepAll()[0]->message;
                }
                ExceptionLog::saveException(new \Exception($error . '<br>Request:<br>' . var_export($arr, true)));
                throw new UserException($error);
            }
        } catch (\Exception $e) {
            ExceptionLog::saveException($e);
            throw new UserException($e->getMessage());
        }
    }

    /**
     * @param $id
     * @param $amount
     * @return bool
     * @throws UserException
     */
    public function submitForSettlement($id, $amount)
    {
        $result = Transaction::submitForSettlement($id, $amount);

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
     * @param $id
     * @return bool
     * @throws UserException
     */
    public function void($id)
    {
        $result = Transaction::void($id);
        if ($result->success) {
            return true;
        }
        throw new UserException($result->errors);
    }

    /**
     * @param int $transactionId
     * @param float|null $amount
     * @return boolean
     * @throws \Exception
     */
    public function refund($transactionId, $amount = null)
    {
        try {
            $transaction = $this->transactionFromBraintree($transactionId);
            if (!$transaction) {
                throw new UserException('Transaction not found');
            }
            if ($transaction->status == Transaction::SUBMITTED_FOR_SETTLEMENT) {
                $result = Transaction::void($transactionId);
                if (!$result->success) {
                    if ($result->message) {
                        $error = $result->message;
                    } else {
                        $error = $result->errors->message;
                    }
                    throw new UserException($error);
                }
            } elseif (in_array($transaction->status, [Transaction::SETTLED, Transaction::SETTLING])) {
                /** @var \Braintree\Result\Error|\Braintree\Result\Successful $result */
                $result = Transaction::refund($transactionId, $amount);
                if ($result->success) {
                    return true;
                } else {
                    if ($result->transaction) {
                        $error = $result->message;
                    } else {
                        $error = $result->errors->deepAll()[0]->message;
                    }
                    throw new UserException($error);
                }
            } else {
                throw new UserException('Unknown transaction status: ' . $transaction->status);
            }
        } catch (\Exception $e) {
            ExceptionLog::saveException($e);
            throw $e;
        }
        return false;
    }

    /**
     * @param $transactionId
     * @return Transaction
     */
    public function transactionFromBraintree($transactionId)
    {
        if (empty($transactionId)) {
            return null;
        }
        try {
            $transaction = Transaction::find($transactionId);
            \Yii::$app->cache->set(
                self::CACHE_TRANSACTION . $transactionId,
                serialize($transaction),
                self::CACHE_TRANSACTION_DURATION
            );
            return $transaction;
        } catch (NotFound $e) {
            \Yii::$app->cache->set(
                self::CACHE_TRANSACTION . $transactionId,
                serialize(null),
                self::CACHE_TRANSACTION_DURATION
            );
            return null;
        } catch (\Exception $e) {
            ExceptionLog::saveException($e);
            return null;
        }

    }

    /**
     * @param Transaction $transaction
     * @return \Braintree\Result\Error|\Braintree\Result\Successful
     */
    public function releaseFromEscrow($transaction)
    {
        if (!$transaction || !$transaction->id) {
            return null;
        }

        if ($transaction->escrowStatus != Transaction::ESCROW_HELD) {
            return null;
        }

        return Transaction::releaseFromEscrow($transaction->id);
    }

    /**
     * @param string $transactionId
     * @return CreditCard
     */
    public function getCreditCardDetailsByTransactionId($transactionId)
    {
        $creditCardDetails = \Yii::$app->cache->get(self::CACHE_TRANSACTIONS_CREDIT_CARD . $transactionId);
        if ($creditCardDetails) {
            return $creditCardDetails;
        }

        $transaction = $this->transaction($transactionId);
        if ($transaction) {
            /** @var CreditCard $creditCardDetails */
            $creditCardDetails = $transaction->creditCardDetails;
            \Yii::$app->cache->set(self::CACHE_TRANSACTIONS_CREDIT_CARD . $transactionId, $creditCardDetails);
            return $creditCardDetails;
        }

        return null;
    }

    /**
     * @param $transactionId
     * @return Transaction
     */
    public function transaction($transactionId)
    {
        $transaction = \Yii::$app->cache->get(self::CACHE_TRANSACTION . $transactionId);
        if ($transaction) {
            return unserialize($transaction);
        }

        return $this->transactionFromBraintree($transactionId);
    }

    /**
     * @param $id
     * @return bool
     */
    public function deleteCustomer($id)
    {
        $customer = $this->getCustomer($id);
        if ($customer) {
            Customer::delete($id);
            $this->clearCustomerCache($id, false);
            return true;
        }
        return false;
    }

    /**
     * @param $id
     * @param bool $registerNew
     * @return bool|Customer
     */
    public function getCustomer($id, $registerNew = false)
    {
        $customer = \Yii::$app->cache->get(self::CACHE_CUSTOMER_KEY . $id);
        if ($customer) {
            return $customer;
        }

        try {
            $customer = Customer::find($id);
        } catch (NotFound $e) {
            if ($registerNew) {
                $customer = $this->registerCustomer(User::findOne($id));
            } else {
                return false;
            }
        } catch (\Exception $e) {
            ExceptionLog::saveException($e);
            return false;
        }
        if (!$customer) {
            return false;
        }

        \Yii::$app->cache->set(self::CACHE_CUSTOMER_KEY . $id, $customer);
        return $customer;
    }

    /**
     * @param int $id
     * @param bool $reset
     * @return bool
     */
    public function clearCustomerCache($id, $reset = true)
    {
        $res = \Yii::$app->cache->delete(self::CACHE_CUSTOMER_KEY . $id);

        if ($reset) {
            $this->getCustomer($id, true);
        }

        return $res;
    }

    /**
     * @param $id
     * @param $attrs
     * @return object
     */
    public function updateCustomer($id, $attrs)
    {
        $res = Customer::update($id, $attrs);
        $this->clearCustomerCache($id);

        return $res;
    }

    /**
     * @param $userId
     * @param $paymentMethodNonce
     * @param bool $isDefault
     * @return bool
     * @throws PaymentDuplicateException
     * @throws UserException
     */
    public function addPaymentMethod($userId, $paymentMethodNonce, $isDefault = false)
    {
        $customer = $this->getCustomer($userId);
        if (!$customer) {
            $customer = $this->registerCustomer(User::findOne($userId));
            if (!$customer) {
                throw new UserException('Can not register customer');
            }
        }

        $options = ['verifyCard' => true, 'makeDefault' => $isDefault];

        $result = PaymentMethod::create(array(
            'customerId' => $customer->id,
            'paymentMethodNonce' => $paymentMethodNonce,
            'options' => $options
        ));


        if (!$result->success) {
            if ($result->message) {
                $error = $result->message;
            } else {
                $error = $result->errors->message;
            }
            throw new UserException($error);
        }

        $sameCards = $this->getCustomerSamePayment($result->paymentMethod, $customer);

        if (count($sameCards) > 1) {
            $this->deletePaymentMethod($result->paymentMethod->token, $customer->id);
            throw new PaymentDuplicateException;
        }

        $this->clearCustomerCache($userId);

        return true;
    }

    /**
     * @param CreditCard $paymentMethod
     * @param Customer $customer
     * @return bool | CreditCard[]
     */
    protected function getCustomerSamePayment($paymentMethod, Customer $customer)
    {
        if (!($paymentMethod instanceof CreditCard)) {
            return false;
        }

        $sameCard = function (CreditCard $card) use ($paymentMethod) {
            return ($card->uniqueNumberIdentifier == $paymentMethod->uniqueNumberIdentifier);
        };

        /** @var Customer $customerInfo */
        $customerInfo = $this->getCustomer($customer->id);
        if (!$customerInfo && !isset($customerInfo->creditCards) or !$customerInfo->creditCards) {
            return false;
        }

        return array_filter($customerInfo->creditCards, $sameCard);
    }

    /**
     * @param $token
     * @param $userId
     * @return bool
     * @throws UserException
     */
    public function deletePaymentMethod($token, $userId)
    {
        try {
            $result = PaymentMethod::delete($token);
        } catch (NotFound $e) {
            throw new UserException('Token not found');
        }
        if (!$result->success) {
            if ($result->message) {
                $error = $result->message;
            } else {
                $error = $result->errors->message;
            }
            throw new UserException($error);
        }

        $this->clearCustomerCache($userId);
        return true;

    }

    /**
     * @param $token
     * @param $userId
     * @param string $nonce
     * @param bool $isDefault
     * @return bool
     * @throws PaymentDuplicateException
     * @throws UserException
     */
    public function updatePaymentMethod($token, $userId, $nonce = '', $isDefault = false)
    {
        $customer = $this->getCustomer($userId);

        $data = [
            'options' => [
                'verifyCard' => true,
                'makeDefault' => $isDefault,
            ]
        ];

        if (!empty($nonce)) {
            $data['paymentMethodNonce'] = $nonce;
        }

        try {
            $result = PaymentMethod::update($token, $data);
        } catch (NotFound $e) {
            throw new UserException('Token not found');
        }

        if (!$result->success) {
            if ($result->message) {
                $error = $result->message;
            } else {
                $error = $result->errors->message;
            }
            throw new UserException($error);
        }

        $sameCards = $this->getCustomerSamePayment($result->paymentMethod, $customer);

        if (count($sameCards) > 1) {
            $card = array_pop($sameCards);

            if ($card->isDefault()) {
                try {
                    PaymentMethod::update($result->paymentMethod->token, [
                        'options' => [
                            'verifyCard' => true,
                            'makeDefault' => $isDefault,
                        ]
                    ]);
                } catch (NotFound $e) {
                    throw new UserException('Token not found');
                }
            };

            $this->deletePaymentMethod($card->token, $customer->id);

            throw new PaymentDuplicateException();
        }

        $this->clearCustomerCache($userId);
        return true;
    }

    /**
     * @param integer $userId
     * @return array
     */
    public function getPaymentMethods($userId)
    {
        $customer = $this->getCustomer($userId, true);
        $paymentMethodList = [];
        if ($customer) {
            if ($customer->creditCards) {
                foreach ($customer->creditCards as $c) {
                    $paymentMethodList[] = $this->getCardJson($c);
                }
            }
            if ($customer->paypalAccounts) {
                foreach ($customer->paypalAccounts as $c) {
                    $paymentMethodList[] = $this->getPaypalJson($c);
                }
            }
        }
        return $paymentMethodList;
    }

    /**
     * @param integer $userId
     * @param string $paymentToken
     * @return array
     */
    public function getPaymentMethodByUserIdPaymentToken($userId, $paymentToken)
    {
        $paymentMethodList = $this->getPaymentMethods($userId);
        foreach ($paymentMethodList as $paymentMethod) {
            if ($paymentMethod['paymentToken'] === $paymentToken) {
                return $paymentMethod;
            }
        }
        return [];
    }

    /**
     * @param CreditCard $c
     * @return array
     */
    protected function getCardJson($c)
    {
        return [
            'isDefault' => $c->isDefault(),
            'paymentToken' => $c->token,
            'paymentMethodInfo' => [
                'type' => self::PAYMENT_METHOD_TYPE_CREDIT_CARD,
                'paymentTypeName' => $c->cardType,
                'last4' => $c->last4,
                'imageUrl' => $c->imageUrl,
                'expirationDate' => $c->expirationDate,
            ]
        ];
    }

    /**
     * @param PayPalAccount $c
     * @return array
     */
    protected function getPaypalJson($c)
    {
        return [
            'isDefault' => $c->isDefault(),
            'paymentToken' => $c->token,
            'paymentMethodInfo' => [
                'type' => self::PAYMENT_METHOD_TYPE_PAY_PAL,
                'paymentTypeName' => 'PayPal',
                'email' => $c->email,
                'imageUrl' => $c->imageUrl,
            ]
        ];
    }

    /**
     * @param $params array
     * @return \Braintree\Result\Error|\Braintree\Result\Successful
     */
    public function createMerchantAccount($params)
    {
        // Braintree Documentation:
        // https://developers.braintreepayments.com/guides/marketplace/onboarding/php
        // https://developers.braintreepayments.com/reference/request/merchant-account/create/php
        $response = MerchantAccount::create($params);
        return $response;
    }

    public function findMerchantAccount($accountId)
    {
        return MerchantAccount::find($accountId);
    }

    protected function getJson($paymentMethod)
    {
        $res = false;

        if ($paymentMethod instanceof CreditCard) {
            $res = $this->getCardJson($paymentMethod);
        } elseif ($paymentMethod instanceof PayPalAccount) {
            $res = $this->getPayPalJson($paymentMethod);
        }

        return $res;
    }

}