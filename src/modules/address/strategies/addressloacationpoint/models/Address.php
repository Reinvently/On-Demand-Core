<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\modules\address\strategies\addressloacationpoint\models;


use reinvently\ondemand\core\modules\location\models\Point;
use reinvently\ondemand\core\modules\location\strategies\modellocationpoint\models\AddressLocationPoint;


/**
 * Class Address
 * @package reinvently\ondemand\core\modules\address\strategies\addressloacationpoint\models
 *
 * @property string locationPointId
 */
class Address extends \reinvently\ondemand\core\modules\address\models\Address
{
    /** @var Point */
    public $locationPointModelClass = AddressLocationPoint::class;

    /**
     * @inheritDoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($this->isChangeLocationPointData($changedAttributes)) {
            $this->saveLocationPoint($insert);
        }
    }

    /**
     * @inheritDoc
     */
    public function afterDelete()
    {
        parent::afterDelete();
        $this->deleteLocationPoint();
    }


    /**
     * @return int integer the number of documents deleted.
     */
    protected function deleteLocationPoint()
    {
        $locationPointModelClass = $this->locationPointModelClass;
        return $locationPointModelClass::deleteAll(
            ['_id' => $this->locationPointId]
        );
    }

    /**
     * @param boolean $insert whether this method called while inserting a record.
     * If false, it means the method is called while updating a record.
     * @return bool
     */
    protected function saveLocationPoint($insert)
    {

        /** @var Point $locationPoint */
        $locationPoint = new $this->locationPointModelClass();

        if (!$insert) {
            $locationPoint->_id = $this->locationPointId;
            $locationPoint->setIsNewRecord(false);
        }
        $locationPoint->externalId = $this->id;
        $locationPoint->latitude = $this->latitude;
        $locationPoint->longitude = $this->longitude;
        $locationPoint->address = $this->address;

        if ($locationPoint->save()) {
            $this->locationPointId = $locationPoint->_id;
            return $this->update(false, ['locationPointId']);
        }
        return false;
    }

    /**
     * @param array $changedAttributes
     * @return bool
     */
    protected function isChangeLocationPointData($changedAttributes)
    {
        $changedAttributes = array_keys($changedAttributes);
        return in_array('latitude', $changedAttributes)
            || in_array('longitude', $changedAttributes)
            || in_array('address', $changedAttributes);

    }

}