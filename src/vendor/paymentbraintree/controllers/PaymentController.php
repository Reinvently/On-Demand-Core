<?php
/**
 * @copyright Reinvently (c) 2018
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */
/**
 * Created by PhpStorm.
 * User: sglushko
 * Date: 16.03.2018
 * Time: 12:01
 */

namespace reinvently\ondemand\core\vendor\paymentbraintree\controllers;


use app\helpers\Braintree;
use reinvently\ondemand\core\controllers\rest\ApiTameController;
use reinvently\ondemand\core\exceptions\UserException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class PaymentController extends ApiTameController
{
    /** @var Braintree */
    protected $braintree;

    public function behaviors()
    {
        $verbs = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'token' => ['get'],
                    'add' => ['post'],
                    'update' => ['put', 'patch'],
                    'delete' => ['delete'],
                    'list' => ['get'],
                ]
            ],
        ];
        return ArrayHelper::merge($verbs, parent::behaviors());
    }

    public function init()
    {
        parent::init();
        $this->braintree = new Braintree();
    }

    public function actionToken()
    {
        $paymentToken = $this->braintree->generateToken($this->getUser()->id);
        if (!$paymentToken) {
            throw new UserException('Error generating token');
        }
        return $this->getTransport()->responseObject(['paymentToken' => $paymentToken]);

    }

    public function actionAdd()
    {
        $paymentNonce = \Yii::$app->request->post('paymentNonce');
        $isDefault = (bool) \Yii::$app->request->post('isDefault', false);
        if (!$paymentNonce) {
            throw new UserException('Payment Nonce is required');
        }

        $result = $this->braintree->addPaymentMethod($this->getUser()->id, $paymentNonce, $isDefault);
        return $this->getTransport()->responseSuccess($result);
    }

    public function actionUpdate()
    {
        $paymentToken = \Yii::$app->request->post('paymentToken');
        $isDefault = (bool) \Yii::$app->request->post('isDefault', false);
        $nonce = \Yii::$app->request->post('paymentNonce');

        if (!$paymentToken) {
            throw new UserException('Payment Token is required');
        }

        $result = $this->braintree->updatePaymentMethod($paymentToken, $this->getUser()->id, $nonce, $isDefault);
        return $this->getTransport()->responseSuccess($result);
    }

    public function actionDelete()
    {
        $paymentToken = \Yii::$app->request->post('paymentToken');
        if (!$paymentToken) {
            throw new UserException('Payment Token is required');
        }

        $customer = $this->braintree->getCustomer($this->getUser()->id);

        if(!$customer) {
            throw new UserException('Customer not found');
        }

        $result = $this->braintree->deletePaymentMethod($paymentToken, $this->getUser()->id);
        return $this->getTransport()->responseSuccess($result);
    }

    public function actionList()
    {
        $paymentMethods = $this->braintree->getPaymentMethods($this->getUser()->id);

        return $this->getTransport()->responseObject($paymentMethods);
    }

}