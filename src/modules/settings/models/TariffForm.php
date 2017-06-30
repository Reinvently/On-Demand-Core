<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\settings\models;

/**
 * Class TariffForm
 * @package reinvently\ondemand\core\modules\settings\models
 */
class TariffForm extends Tariff
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['name', 'price'], 'required'],
            [['price', 'employeePrice', 'additionalPrice'], 'double'],
            [['employeePrice', 'additionalPrice', 'description'], 'safe'],
        ];
    }

    public static function tableName()
    {
        return Tariff::tableName();
    }

    public function afterFind()
    {
        parent::afterFind();
        $this->price = $this->price / 100;
        $this->employeePrice = $this->employeePrice / 100;
        $this->additionalPrice = $this->additionalPrice / 100;
    }

    public function save($runValidation = true, $attributeNames = null)
    {
        $tariff = new Tariff();
        $tariff->setAttributes($this->getAttributes());
        if ($this->getOldAttributes()) {
            $tariff->setOldAttributes($this->getOldAttributes());
        }
        $tariff->price = (int) round($this->price * 100);
        $tariff->employeePrice = (int) round($this->employeePrice * 100);
        $tariff->additionalPrice = (int) round($this->additionalPrice * 100);

        return $tariff->save();
    }




}