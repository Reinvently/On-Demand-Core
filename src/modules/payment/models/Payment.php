<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\modules\payment\models;

use reinvently\ondemand\core\components\model\CoreModel;
use reinvently\ondemand\core\components\transport\ApiInterface;
use reinvently\ondemand\core\components\transport\ApiTransportTrait;

/**
 * Class Payment
 * @package reinvently\ondemand\core\modules\payment\models
 *
 * @property int id
 * @property int orderId
 * @property int price
 * @property int status
 * @property int createdAt
 * @property int updatedAt
 * @property string transactionId
 * @property string description
 */
class Payment extends CoreModel implements ApiInterface
{
    use ApiTransportTrait;

    const STATUS_NEW = 1;
    const STATUS_AUTH = 2;
    const STATUS_SALE = 3;
    const STATUS_CANCEL_AUTH = 4;
    const STATUS_CANCEL_SALE = 5;
    const STATUS_ERROR = 6;

    public static $statuses = [
        self::STATUS_NEW => 'New',
        self::STATUS_AUTH => 'Auth',
        self::STATUS_SALE => 'Sale',
        self::STATUS_CANCEL_AUTH => 'Cancel auth',
        self::STATUS_CANCEL_SALE => 'Cancel sale',
        self::STATUS_ERROR => 'Error',
    ];

    /**
     * @return string
     */
    public static function tableName()
    {
        return 'payment';
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['orderId', 'price', 'status'], 'required'],
            [['price'], 'integer'],
            [['transactionId', 'description'], 'string', 'max' => 255],
            [['orderId', 'price', 'transactionId', 'description', 'status'], 'safe'],
        ];
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->createdAt = time();
        }
        if ($this->getDirtyAttributes()) {
            $this->updatedAt = time();
        }
        return parent::beforeSave($insert);
    }

    /**
     * @return array
     */
    public function getItemForApi()
    {
        return [
            'id' => $this->id,
            'orderId' => $this->orderId,
            'price' => $this->price,
            'status' => $this->status,
            'description' => $this->description,
            'transactionId' => $this->transactionId,
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
            'price' => $this->price,
            'status' => $this->status,
        ];
    }

}