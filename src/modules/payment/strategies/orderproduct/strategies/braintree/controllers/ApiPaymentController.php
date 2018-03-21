<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\modules\payment\strategies\orderproduct\strategies\braintree\controllers;


use reinvently\ondemand\core\modules\payment\strategies\orderproduct\strategies\braintree\models\Payment;
use reinvently\ondemand\core\vendor\paymentbraintree\BraintreeContainer;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class ApiPaymentController extends \reinvently\ondemand\core\modules\payment\controllers\ApiPaymentController
{

    public $modelClass = Payment::class;

    /**
     * @return Payment
     */
    public function getPaymentModel()
    {
        return new $this->modelClass();
    }

    public function behaviors()
    {
        $verbs = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'token' => ['GET'],
                    'pay' => ['POST'],
                ]
            ],
        ];
        return ArrayHelper::merge($verbs, parent::behaviors());
    }

    public function actionToken()
    {
        $c = new BraintreeContainer();
        $c->setUserId(\Yii::$app->user->id);

        $this->getPaymentModel()->makeToken($c);

        if ($c->hasErrors()) {
            return $c;
        }

        $token = $c->getToken();

        return $this->getTransport()->responseScalar($token);
    }

    public function actionPay()
    {
        $nonce = \Yii::$app->request->post('nonce');
        $orderId = \Yii::$app->request->post('orderId');

        /** @var Payment $paymentModel */
        $paymentModel = $this->getPaymentModel();
        $paymentModel->orderId = $orderId;
        $res = $paymentModel->pay($nonce);

        if ($res->hasErrors()) {
            return $res;
        }

        return $this->getTransport()->responseScalar();
    }

}