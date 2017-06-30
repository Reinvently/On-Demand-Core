<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\components\model;


use reinvently\ondemand\core\controllers\rest\RestModelTrait;
use yii\mongodb\ActiveRecord;

/**
 * Class CoreMongoDbModel
 * @package reinvently\ondemand\core\components\model
 *
 * @property string _id
 */
abstract class CoreMongoDbModel extends ActiveRecord
{
    use RestModelTrait;

    /**
     * For replace some fake attributes on real ones
     * example: ['fakeId' => 'realId']
     * @return array
     */
    public function replacementAttributes()
    {
        return [];
    }

    /**
     * Attributes auto convert to integer when set
     * @return array
     */
    public function integerAttributes()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function __set($name, $value)
    {
        $name = $this->getRealAttributeName($name);
        $value = $this->getValueAttribute($name, $value);
        parent::__set($name, $value);
    }

    /**
     * @inheritDoc
     */
    public function __get($name)
    {
        $name = $this->getRealAttributeName($name);
        if ($name == '_id') {
            return $this->getMongoId();
        }
        return parent::__get($name);
    }

    /**
     * @return string string
     */
    protected function getMongoId()
    {
        return (string) parent::__get('_id');
    }

    /**
     * replaceAttribute
     * @param string $name fake attribute name
     * @return string real attribute name
     */
    protected function getRealAttributeName($name)
    {
        if (array_key_exists($name, $this->replacementAttributes())) {
            $name = $this->replacementAttributes()[$name];
        }
        return $name;
    }

    /**
     * replaceAttribute
     * @param string $name real attribute name
     * @return string fake attribute name
     */
    protected function getFakeAttributeName($name)
    {
        if (in_array($name, $this->replacementAttributes())) {
            $name = array_search($name, $this->replacementAttributes());
        }
        return $name;
    }

    /**
     * method will convert Attribute value to other type if need
     * @param string $name attribute name
     * @param mixed $value attribute value
     * @return mixed
     */
    protected function getValueAttribute($name, $value)
    {
        if (in_array($name, $this->integerAttributes())) {
            $value = (int) $value;
        }
        return $value;
    }

    /**
     * @inheritDoc
     */
    public function addError($attribute, $error = '')
    {
        $attribute = $this->getFakeAttributeName($attribute);
        parent::addError($attribute, $error);
    }


}