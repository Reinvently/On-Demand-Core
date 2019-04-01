<?php
/**
 * @copyright Reinvently (c) 2018
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\modules\address\models;


use reinvently\ondemand\core\components\model\CoreModel;
use reinvently\ondemand\core\components\transport\ApiInterface;
use reinvently\ondemand\core\components\transport\ApiTransportTrait;
use reinvently\ondemand\core\modules\servicearea\models\Zip;
use yii\db\ActiveQuery;

/**
 * Class Address
 * @package reinvently\ondemand\core\modules\address\models
 *
 * @property int id
 * @property int userId
 * @property double latitude
 * @property double longitude
 * @property string address
 * @property int createdAt
 * @property int updatedAt
 * @property string $country
 * @property string $zip
 * @property string $stateCode
 * @property string $city
 *
 * @property Zip $zipObject
 */
class Address extends CoreModel implements ApiInterface
{
    use ApiTransportTrait;

    public $zipModelClass = Zip::class;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['address', 'latitude', 'longitude'], 'required'],
            [['latitude', 'longitude'], 'double'],
            [['country', 'zip'], 'required'],
            [['country', 'zip', 'stateCode', 'city'], 'string', 'max' => 255],
        ];
    }

    public function beforeSave($insert)
    {
        if ($this->getIsNewRecord()) {
            $this->createdAt = time();
            $this->userId = \Yii::$app->user->id;
        }
        if ($this->getDirtyAttributes()) {
            $this->updatedAt = time();
        }
        return parent::beforeSave($insert);
    }

    /**
     * @return ActiveQuery
     */
    public function getZipObject()
    {
        return $this->hasOne($this->zipModelClass, ['zip' => 'zip']);
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
            'country' => $this->country,
            'zip' => $this->zip,
            'stateCode' => $this->stateCode,
            'city' => $this->city,
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
            'country' => $this->country,
            'zip' => $this->zip,
        ];
    }
}