<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\modules\settings\models;


use reinvently\ondemand\core\components\model\CoreModel;
use reinvently\ondemand\core\components\transport\ApiInterface;
use reinvently\ondemand\core\components\transport\ApiTransportTrait;

/**
 * Class Tariff
 * @package reinvently\ondemand\core\modules\settings\models
 *
 * @property int id
 * @property string name
 * @property int price
 * @property int employeePrice
 * @property int additionalPrice
 * @property string description
 *
 */
class Tariff extends CoreModel implements ApiInterface
{
    use ApiTransportTrait;



    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['name', 'price'], 'required'],
            [['price', 'employeePrice', 'additionalPrice'], 'integer'],
            [['employeePrice', 'additionalPrice', 'description'], 'safe'],
        ];
    }


    /**
     * @return array
     */
    public function getItemForApi()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'employeePrice' => $this->employeePrice,
            'additionalPrice' => $this->additionalPrice,
            'description' => $this->description,
        ];
    }

    /**
     * @return array
     */
    public function getItemShortForApi()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'employeePrice' => $this->employeePrice,
            'additionalPrice' => $this->additionalPrice,
        ];
    }
}