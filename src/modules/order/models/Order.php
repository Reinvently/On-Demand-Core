<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\order\models;

use reinvently\ondemand\core\components\statemachine\StateMachineModel;
use reinvently\ondemand\core\components\transport\ApiTransportTrait;
use reinvently\ondemand\core\modules\address\models\Address;
use reinvently\ondemand\core\modules\orderproduct\models\OrderProduct;
use reinvently\ondemand\core\modules\user\models\User;
use yii\db\BaseActiveRecord;


/**
 * Class Order
 * @package reinvently\ondemand\core\modules\order\models
 *
 * @property int id
 * @property int $status
 * @property int $v version of document
 * @property int userId
 * @property int addressId
 * @property string firstName
 * @property string lastName
 * @property string phone
 * @property int createdAt
 * @property int updatedAt
 *
 * @property OrderProduct[] orderProducts
 * @property User user
 * @property Address address
 */
abstract class Order extends StateMachineModel
{
    use ApiTransportTrait;

    public $orderProductModelClass = OrderProduct::class;

    /* todo example of state machine
    public function getStateMachineParams()
    {
        return [
            'class' => StateMachine::class,
            'states' => [
                [
                    'class' => 'reinvently\ondemand\core\modules\order\models\OrderPendingState',
                    'name' => static::STATUS_PENDING,
                    'transitsTo' => [static::STATUS_ACTIVE],
                ],
                [
                    'class' => 'reinvently\ondemand\core\modules\order\models\OrderActiveState',
                    'name' => static::STATUS_ACTIVE,
                    'transitsTo' => [static::STATUS_COMPLETED],
                ],
                [
                    'class' => 'reinvently\ondemand\core\modules\order\models\OrderCompletedState',
                    'name' => static::STATUS_COMPLETED,
                ]
            ],
            'defaultStateName' => static::STATUS_PENDING,
            'stateName' => $this->status,
            'columnName' => 'status',
            'checkTransitionMap' => true,
        ];
    }*/

    public static function tableName()
    {
        return '{{%order}}';
    }

    public function rules()
    {
        return [
            [['status', 'userId'], 'required'],
            [['firstName', 'lastName', 'phone', 'addressId'], 'safe'],
        ];
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->createdAt = time();
        }
        $this->updatedAt = time();
        return parent::beforeSave($insert);
    }

    public function beforeValidate()
    {
        if ($this->getIsNewRecord() and !$this->userId) {
            $this->userId = \Yii::$app->user->id;
        }
        return parent::beforeValidate();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderProducts()
    {
        /** @var BaseActiveRecord $class */
        $class = $this->orderProductModelClass;
        return $this->hasMany($class::className(), ['orderId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        /** @var BaseActiveRecord $class */
        $class = \Yii::$app->user->identityClass;
        return $this->hasOne($class::className(), ['id' => 'userId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAddress()
    {
        /** @var BaseActiveRecord $class */
        return $this->hasOne(Address::className(), ['id' => 'addressId']);
    }

    /**
     * @return array
     */
    public function getItemForApi()
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'userId' => $this->userId,
            'addressId' => $this->addressId,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'phone' => $this->phone,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }

    /**
     * @return array
     */
    public function getItemShortForApi()
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'userId' => $this->userId,
            'addressId' => $this->addressId,
            'firstName' => $this->firstName,
            'phone' => $this->phone,
        ];
    }
}

