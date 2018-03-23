<?php
/**
 * @copyright Reinvently (c) 2018
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

/**
 * Created by PhpStorm.
 * User: sglushko
 * Date: 20.03.2018
 * Time: 16:10
 */
namespace reinvently\ondemand\core\modules\invoice\models;

use reinvently\ondemand\core\components\model\CoreModel;
use reinvently\ondemand\core\components\transport\ApiInterface;
use reinvently\ondemand\core\components\transport\ApiTransportTrait;

/**
 * This is the model class for table "payment_method".
 *
 * @property int $id
 * @property int $orderId
 * @property int $userId
 * @property string $token
 * @property int $percent
 */
class OrderPaymentMethod extends CoreModel implements ApiInterface
{
    use ApiTransportTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_payment_method';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userId', 'orderId', 'percent', 'token'], 'required'],
            [['userId', 'orderId', 'percent'], 'integer'],
            [['token'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'orderId' => 'Order ID',
            'token' => 'Token',
            'percent' => 'Percent',
        ];
    }

    /**
     * @return array
     */
    public function getItemForApi()
    {
        return [
            'id' => $this->id,
            'userId' => $this->userId,
            'orderId' => $this->orderId,
            'token' => $this->token,
            'percent' => $this->percent,
        ];
    }

    /**
     * @return array
     */
    public function getItemShortForApi()
    {
        return $this->getItemForApi();

    }
}
