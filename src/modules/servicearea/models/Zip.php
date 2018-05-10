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
use reinvently\ondemand\core\modules\address\models\Address;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "zip".
 *
 * @property integer $id
 * @property string $country
 * @property string $zip
 * @property string $stateCode
 * @property string $city
 * @property integer $serviceAreaId
 *
 * @property ServiceArea serviceArea
 */
class Zip extends CoreModel implements ApiInterface
{
    use ApiTransportTrait;

    public $serviceAreaModelClass = ServiceArea::class;
    public $addressModelClass = Address::class;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'zip';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['country', 'zip'], 'required'],
            [['serviceAreaId'], 'integer'],
            [['country', 'zip', 'stateCode', 'city'], 'string', 'max' => 255],
            [['country', 'zip'], 'unique', 'targetAttribute' => ['country', 'zip'], 'message' => 'The combination of Country and Zip has already been taken.'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'country' => 'Country',
            'zip' => 'Zip',
            'stateCode' => 'State Code',
            'city' => 'City',
            'serviceAreaId' => 'Service Area ID',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getServiceArea()
    {
        return $this->hasOne($this->serviceAreaModelClass, ['id' => 'serviceAreaId']);
    }

    /**
     * @return ActiveQuery
     */
    public function getAddresses()
    {
        return $this->hasMany($this->addressModelClass, ['zip' => 'zip'])->inverseOf('zipObject');
    }

    /**
     * @return array
     */
    public function getItemForApi()
    {
        return [
            'id' => $this->id,
            'country' => $this->country,
            'zip' => $this->zip,
            'stateCode' => $this->stateCode,
            'city' => $this->city,
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
