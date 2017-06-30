<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\modules\product\models;


use reinvently\ondemand\core\components\model\CoreModel;
use reinvently\ondemand\core\components\transport\ApiInterface;
use reinvently\ondemand\core\components\transport\ApiTransportTrait;

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
 * @property string image
 *
 */
class Product extends CoreModel implements ApiInterface
{
    use ApiTransportTrait;

    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%product}}';
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['price', 'categoryId'], 'integer'],
            [['categoryId', 'sort', 'description', 'image', 'price'], 'safe'],
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
     * @return array
     */
    public function getItemForApi()
    {
        return [
            'id' => $this->id,
            'categoryId' => $this->categoryId,
            'sort' => $this->sort,
            'title' => $this->title,
            'description' => $this->description,
            'image' => $this->image,
            'price' => $this->price,
        ];
    }

    /**
     * @return array
     */
    public function getItemShortForApi()
    {
        return [
            'id' => $this->id,
            'sort' => $this->sort,
            'title' => $this->title,
            'image' => $this->image,
            'price' => $this->price,
        ];
    }

}