<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\modules\location\strategies\modellocationpoint\models;


use reinvently\ondemand\core\modules\location\models\Point;

/**
 * Class UserLocationPoint
 * @package reinvently\ondemand\core\modules\location\strategies\modellocationpoint\models
 *
 * @property int userId
 */
class UserLocationPoint extends Point
{

    /**
     * @return int
     */
    public static function modelType()
    {
        $classType = static::$classType;
        return $classType::USER;
    }

    /**
     * @inheritDoc
     */
    public function replacementAttributes()
    {
        return ['userId' => 'externalId'];
    }

    /**
     * @inheritDoc
     */
    public function rules()
    {
        $array = parent::rules();
        $array[] = ['userId', 'safe'];
        return $array;
    }

    /**
     * @return array
     */
    public function getItemForApi()
    {
        $array = parent::getItemForApi();

        $array['userId'] = $this->userId;
        unset($array['type']);
        unset($array['externalId']);

        return $array;
    }

    /**
     * @return array
     */
    public function getItemShortForApi()
    {
        $array = parent::getItemShortForApi();

        $array['userId'] = $this->userId;
        unset($array['type']);
        unset($array['externalId']);

        return $array;
    }

}