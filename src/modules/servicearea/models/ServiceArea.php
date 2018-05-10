<?php
/**
 * @copyright Reinvently (c) 2018
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\modules\servicearea\models;

use reinvently\ondemand\core\components\model\CoreModel;
use reinvently\ondemand\core\components\transport\ApiInterface;
use reinvently\ondemand\core\components\transport\ApiTransportTrait;
use reinvently\ondemand\core\modules\order\models\Order;
use reinvently\ondemand\core\modules\product\models\Product;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "service_area".
 *
 * @property integer $id
 * @property integer $type
 * @property integer $status
 * @property string $name
 *
 * @property Zip[] $zips
 * @property Order[] $orders
 * @property Product[] $products
 */
class ServiceArea extends CoreModel implements ApiInterface
{
    use ApiTransportTrait;

    public $zipModelClass = Zip::class;
    public $productModelClass = Product::class;
    public $orderModelClass = Order::class;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'service_area';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'status'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'status' => 'Status',
            'name' => 'Name',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getZips()
    {
        return $this->hasMany($this->zipModelClass, ['serviceAreaId' => 'id'])->inverseOf('serviceArea');
    }

    /**
     * @return ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany($this->productModelClass, ['serviceAreaId' => 'id'])->inverseOf('serviceArea');
    }

    /**
     * @return ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany($this->orderModelClass, ['serviceAreaId' => 'id'])->inverseOf('serviceArea');
    }

    /**
     * @return array
     */
    public function getItemForApi()
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'status' => $this->status,
            'name' => $this->name,
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
