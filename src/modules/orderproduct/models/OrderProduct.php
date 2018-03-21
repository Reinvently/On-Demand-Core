<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\modules\orderproduct\models;


use reinvently\ondemand\core\components\model\CoreModel;
use reinvently\ondemand\core\components\transport\ApiInterface;
use reinvently\ondemand\core\components\transport\ApiTransportTrait;
use reinvently\ondemand\core\controllers\rest\ApiController;
use reinvently\ondemand\core\exceptions\AccessDenyHttpException;
use reinvently\ondemand\core\modules\order\models\Order;
use reinvently\ondemand\core\modules\product\models\Product;
use reinvently\ondemand\core\modules\role\models\Role;
use yii\base\Model;
use yii\db\BaseActiveRecord;
use yii\web\NotFoundHttpException;

/**
 * Class OrderProduct
 * @package reinvently\ondemand\core\modules\orderproduct\models
 *
 * @property int id
 * @property int orderId
 * @property int productId
 * @property int price
 * @property int count
 *
 * @property Product product
 * @property Order order
 */
class OrderProduct extends CoreModel implements ApiInterface
{
    use ApiTransportTrait;

    /**
     * @return string
     */
    public static function tableName()
    {
        return 'order_product';
    }

    public $productModelClass = Product::class;
    public $orderModelClass = Order::class;

    public static function salesReceipt($orderId)
    {
        $total = 0;
        /** @var OrderProduct[] $items */
        $array = OrderProduct::findAll(['orderId' => $orderId]);

        foreach ($array as $item) {
            $total += $item->price * $item->count;
        }

        return $total;
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['orderId', 'productId', 'price', 'count'], 'required'],
            [['orderId', 'productId', 'price', 'count'], 'integer'],
            [['orderId', 'productId'], 'unique', 'targetAttribute' => ['orderId', 'productId']],
            [['orderId', 'productId', 'count'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return [
            Model::SCENARIO_DEFAULT => ['orderId', 'productId', 'count', '!price'],
            ApiController::UPDATE_SCENARIO => ['orderId', 'productId', 'count', '!price'],
            ApiController::CREATE_SCENARIO => ['orderId', 'productId', 'count', '!price'],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        /** @var BaseActiveRecord $class */
        $class = $this->productModelClass;
        return $this->hasOne($class::class, ['id' => 'productId']);
    }

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
        if ($this->getScenario() != Model::SCENARIO_DEFAULT) {
            if (!$this->product) {
                throw new NotFoundHttpException('Product not found');
            }
            if (!$this->order) {
                throw new NotFoundHttpException('Order not found');
            }

            $this->price = $this->product->getPrice();
        }

        if (\Yii::$app->user->getIdentity()->roleId != Role::ADMIN and $this->order->userId != \Yii::$app->user->id) {
            throw new AccessDenyHttpException();
        }
        return parent::beforeValidate();
    }


    /**
     * @return array
     */
    public function getItemForApi()
    {
        return [
            'id' => $this->id,
            'orderId' => $this->orderId,
            'productId' => $this->productId,
            'price' => $this->price,
            'count' => $this->count,
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