<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\modules\category\models;


use reinvently\ondemand\core\components\model\CoreModel;
use reinvently\ondemand\core\components\transport\ApiInterface;
use reinvently\ondemand\core\components\transport\ApiTransportTrait;

/**
 * Class Category
 * @package reinvently\ondemand\core\modules\category\models
 *
 * @property int id
 * @property int parentId
 * @property int sort
 * @property string title
 * @property string description
 * @property string image
 *
 */
class Category extends CoreModel implements ApiInterface
{
    use ApiTransportTrait;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['parentId', 'sort', 'description', 'image'], 'safe'],
        ];
    }

    /**
     * @return string
     */
    public static function tableName()
    {
        return 'category';
    }

    /**
     * @return array
     */
    public function getItemForApi()
    {
        return [
            'id' => $this->id,
            'parentId' => $this->parentId,
            'sort' => $this->sort,
            'title' => $this->title,
            'description' => $this->description,
            'image' => $this->image,
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
        ];
    }
}