<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\order\strategies\orderdriver\traits;

use reinvently\ondemand\core\modules\order\models\Order;

/**
 * Class OrderDriver
 * @package reinvently\ondemand\core\modules\order\strategies\orderdriver\traits
 *
 * @property int $driverId
 */
trait OrderDriver
{
    /**
     * @return array
     */
    public function rules()
    {
        /** @var Order $parent */
        $parent = parent::class;
        $rules = $parent::rules();
        $rules[] = ['driverId', 'safe'];
        return $rules;
    }

    /**
     * @return array
     */
    public function getItemForApi()
    {
        /** @var Order $parent */
        $parent = parent::class;
        return $parent::getItemForApi() + ['driverId' => $this->driverId];
    }

}