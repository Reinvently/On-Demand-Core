<?php
/**
 * @copyright Reinvently (c) 2018
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\modules\product\models;


use reinvently\ondemand\core\components\model\CoreModel;
use reinvently\ondemand\core\components\transport\ApiInterface;
use reinvently\ondemand\core\components\transport\ApiTransportTrait;
use reinvently\ondemand\core\modules\servicearea\models\ServiceArea;

/**
 * Class Product
 * @package reinvently\ondemand\core\modules\product\models
 *
 * @property int id
 * @property int categoryId
 * @property int sort
 * @property int price
 * @property string title
 * @property string description
 * @property string $shortDescription
 * @property boolean $isOneTimePay
 * @property int $serviceAreaId
 *
 * @property ServiceArea serviceArea
 */
class Product extends CoreModel implements ApiInterface
{
    use ApiTransportTrait;

    public $serviceAreaModelClass = ServiceArea::class;

    /**
     * @return string
     */
    public static function tableName()
    {
        return 'product';
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['price', 'categoryId', 'serviceAreaId'], 'integer'],
            [['serviceAreaId', 'categoryId', 'sort', 'description', 'image', 'price'], 'safe'],
            [['isOneTimePay'], 'boolean'],
            [['title'], 'string', 'max' => 255],
            [['shortDescription', 'description'], 'string', 'max' => 0xFFFE],
        ];
    }

    /**
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceArea()
    {
        return $this->hasOne($this->serviceAreaModelClass, ['id' => 'serviceAreaId']);
    }

    /**
     * @return array
     */
    public function getItemForApi()
    {
        return [
            'id' => $this->id,
            'categoryId' => $this->categoryId,
            'sort' => $this->sort,
            'title' => $this->title,
            'shortDescription' => $this->shortDescription,
            'description' => $this->description,
            'price' => $this->price,
            'isOneTimePay' => $this->isOneTimePay,
            'serviceAreaId' => $this->serviceAreaId,
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