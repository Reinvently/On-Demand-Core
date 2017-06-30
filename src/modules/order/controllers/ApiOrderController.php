<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\modules\order\controllers;


use reinvently\ondemand\core\components\statemachine\controllers\ApiStateMachineController;
use reinvently\ondemand\core\controllers\rest\ApiController;
use reinvently\ondemand\core\exceptions\AccessDenyHttpException;
use reinvently\ondemand\core\modules\order\models\Order;
use reinvently\ondemand\core\modules\role\models\Role;
use yii\web\NotFoundHttpException;

class ApiOrderController extends ApiStateMachineController
{
    //public $modelClass = Order::class;
    /**
     * @return \reinvently\ondemand\core\components\statemachine\StateMachineModel
     */
    public function getModelClass()
    {
        return $this->modelClass;
    }

    public function behaviors()
    {
        $verbs = [
            'verbs' => [
                'actions' => [
                    'products' => ['get', 'delete'],
                ]
            ],
        ];
        return array_merge_recursive(parent::behaviors(), $verbs);
    }

    public function actionProducts($id)
    {
        $className = $this->getModelClass();

        $order = $className::findOne($id);
        if (!$order) {
            throw new NotFoundHttpException();
        }

        if (\Yii::$app->user->getIdentity()->roleId != Role::ADMIN and $order->userId != \Yii::$app->user->id) {
            throw new AccessDenyHttpException();
        }

        if ($order->orderProduct) {
            foreach($order->orderProduct as $op) {
                $op->delete();
            }
        }

    }

}