<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\modules\payment\strategies\orderproduct\models;
use reinvently\ondemand\core\exceptions\AccessDenyHttpException;
use reinvently\ondemand\core\modules\order\models\Order;
use yii\db\BaseActiveRecord;
use yii\web\NotFoundHttpException;

/**
 * Class Payment
 * @package reinvently\ondemand\core\modules\payment\strategies\orderproduct\models
 *
 * @property Order order
 */
class Payment extends \reinvently\ondemand\core\modules\payment\models\Payment
{
    public $orderModelClass = Order::class;

//    /**
//     * @return array the validation rules.
//     */
//    public function rules()
//    {
//        return [
//            [['order'], 'required'],
//        ];
//    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        /** @var BaseActiveRecord $class */
        $class = $this->orderModelClass;
        return $this->hasOne($class::class, ['id' => 'orderId']);
    }

    /**
     * @return bool
     * @throws AccessDenyHttpException
     * @throws NotFoundHttpException
     */
    public function beforeValidate()
    {
        if (!$this->order) {
            throw new NotFoundHttpException('Order not found'); // todo общее исключение проверка на обязательность order в rules
        }

        //todo add scenarios for admin
        if ($this->order->userId != \Yii::$app->user->id) {
            //todo не тот пользователь
            throw new AccessDenyHttpException();
        }
        return parent::beforeValidate();
    }

} 