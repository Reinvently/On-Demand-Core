<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\modules\address\models;


use reinvently\ondemand\core\components\model\CoreModel;
use reinvently\ondemand\core\components\transport\ApiInterface;
use reinvently\ondemand\core\components\transport\ApiTransportTrait;

/**
 * Class Address
 * @package reinvently\ondemand\core\modules\address\models
 *
 * @property int id
 * @property int userId
 * @property flout latitude
 * @property flout longitude
 * @property string address
 * @property int createdAt
 * @property int updatedAt
 *
 */
class Address extends CoreModel implements ApiInterface
{
    use ApiTransportTrait;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['address', 'latitude', 'longitude'], 'required'],
            [['latitude', 'longitude'], 'double']
        ];
    }

    public function beforeSave($insert)
    {
        if ($this->getIsNewRecord()) {
            $this->createdAt = time();
            $this->userId = \Yii::$app->user->id;
        }
        $this->updatedAt = time();
        return parent::beforeSave($insert);
    }

    /**
     * @return array
     */
    public function getItemForApi()
    {
        return [
            'id' => $this->id,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
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
            'address' => $this->address,
        ];
    }
}