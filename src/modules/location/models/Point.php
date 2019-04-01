<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\modules\location\models;


use reinvently\ondemand\core\components\model\CoreMongoDbModel;
use reinvently\ondemand\core\components\transport\ApiInterface;
use reinvently\ondemand\core\components\transport\ApiTransportTrait;
use yii\db\ActiveQueryInterface;

/**
 * Class Point
 * @package reinvently\ondemand\core\modules\location\models
 *
 *
 * @property int type
 * @property int externalId
 * @property object location
 * @property string address
 * @property int createdAt
 * @property int updatedAt
 *
 */
abstract class Point extends CoreMongoDbModel implements ApiInterface, TypeInterfaces
{
    Use ApiTransportTrait;

    /** @var Type */
    public static $classType = Type::class;

    /** @var float */
    public $latitude;

    /** @var float */
    public $longitude;

    /**
     * @inheritDoc
     */
    public static function collectionName()
    {
        return 'locationPoint';
    }

    /**
     * @inheritDoc
     */
    public static function find()
    {
        $query = parent::find();
        if (static::modelType()) {
            $query->andWhere(['type' => static::modelType()]);
        }
        return $query;
    }

    /**
     * @param float $latitude
     * @param float $longitude
     * @param float $radius
     * @return ActiveQueryInterface
     */
    public static function findByTypeCircle($latitude, $longitude, $radius)
    {
        return static::find()->where([
            'type' => static::modelType(),
            'location' => static::getGeoWithin($latitude, $longitude, $radius),
        ]);
    }

    /**
     * @param float $latitude
     * @param float $longitude
     * @param float $radius
     * @return ActiveQueryInterface
     */
    public static function findByCircle($latitude, $longitude, $radius)
    {
        return static::find()->where([
            'location' => static::getGeoWithin($latitude, $longitude, $radius),
        ]);
    }

    /**
     * mongoDb condition for search by circle
     * @param float $latitude
     * @param float $longitude
     * @param float $radius
     * @return array
     */
    protected static function getGeoWithin($latitude, $longitude, $radius)
    {
        return [
            '$geoWithin' => [
                '$centerSphere' => [
                    [(float)$latitude, (float)$longitude],
                    (float)$radius / 6378.137 // approximate equatorial radius of the earth, 6378.137 kilometers
                ]
            ]
        ];
    }

    public function searchFind()
    {
        return parent::find();
    }

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();

        $this->type = $this->getType();
    }

    /**
     * @inheritDoc
     */
    public function attributes()
    {
        return ['_id', 'type', 'externalId', 'location', 'address', 'createdAt', 'updatedAt'];
    }

    /**
     * @inheritDoc
     */
    public function integerAttributes()
    {
        return ['type', 'externalId', 'createdAt', 'updatedAt'];
    }

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            [['type', 'externalId'], 'required'],
            [['externalId'], 'unique', 'targetAttribute' => ['type', 'externalId']],
            [['type', 'externalId', 'latitude', 'longitude', 'address'], 'safe'],
        ];
    }


    /**
     * @inheritDoc
     */
    public function beforeValidate()
    {
        $this->prepareSave();

        return parent::beforeValidate();
    }

    /**
     * @inheritDoc
     */
    public function beforeSave($insert)
    {
        if ($this->getIsNewRecord()) {
            $this->createdAt = time();
        }
        if ($this->getDirtyAttributes()) {
            $this->updatedAt = time();
        }
        return parent::beforeSave($insert);
    }

    /**
     * @inheritDoc
     */
    public function afterFind()
    {
        $this->unpackLocationPoint();

        parent::afterFind();
    }

    /**
     * @return array
     */
    public function getItemForApi()
    {

        return [
            '_id' => $this->_id,
            'type' => $this->type,
            'externalId' => $this->externalId,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'address' => $this->address,
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
            '_id' => $this->_id,
            'type' => $this->type,
            'externalId' => $this->externalId,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'address' => $this->address,
        ];
    }

    /**
     * @return array
     */
    public function getItemIdForApi()
    {
        return [
            '_id' => $this->_id,
        ];
    }

    /**
     * @return int
     */
    protected function getType()
    {
        return static::modelType();
    }

    /**
     *
     */
    protected function prepareSave()
    {
        $this->packLocationPoint();
    }

    /**
     * @return bool
     */
    protected function packLocationPoint()
    {
        if (isset($this->latitude) && isset($this->longitude)) {
            $location = [];
            $location['type'] = 'Point';
            $location['coordinates'] = [
                (float)$this->latitude,
                (float)$this->longitude
            ];

            $this->location = $location;

            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    protected function unpackLocationPoint()
    {
        if (
            isset($this->location['type'])
            && $this->location['type'] == 'Point'
            && isset($this->location['coordinates'][0])
            && isset($this->location['coordinates'][1])
        ) {
            $this->latitude = $this->location['coordinates'][0];
            $this->longitude = $this->location['coordinates'][1];
            return true;
        }
        return false;
    }

}