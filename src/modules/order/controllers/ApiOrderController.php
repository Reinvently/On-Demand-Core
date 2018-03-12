<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\modules\order\controllers;


use reinvently\ondemand\core\components\statemachine\controllers\ApiStateMachineController;
use reinvently\ondemand\core\modules\order\models\Order;

class ApiOrderController extends ApiStateMachineController
{
    public $modelClass = Order::class;

    /**
     * @return \reinvently\ondemand\core\components\statemachine\StateMachineModel
     */
    public function getModelClass()
    {
        return $this->modelClass;
    }

}