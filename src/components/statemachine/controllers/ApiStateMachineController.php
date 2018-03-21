<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\components\statemachine\controllers;

use reinvently\ondemand\core\components\statemachine\StateMachineModel;
use reinvently\ondemand\core\controllers\rest\ApiController;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;

abstract class ApiStateMachineController extends ApiController
{
    /**
     * @return StateMachineModel
     */
    abstract public function getModelClass();

    public function behaviors()
    {
        $verbs = [
            'verbs' => [
                'class' => \yii\filters\VerbFilter::class,
                'actions' => [
                    'state' => ['get', 'post'],
                    'state-list' => ['get'],
                ]
            ],
        ];
        return ArrayHelper::merge($verbs, parent::behaviors());
    }

    public function actionState()
    {
        switch (Yii::$app->request->getMethod()) {
            case 'GET':
                $id = Yii::$app->request->get('id');
                if (!$id) {
                    return $this->getTransport()->responseMessage('id is required');
                }
                $object = $this->_getStateMachineObject($id);
                if (!$object) {
                    \Yii::$app->response->setStatusCode(404);
                    return $this->getTransport()->responseMessage('Object not found');
                }
                return $this->getTransport()->responseObject($object->getApiState());
            case 'POST':
                $id = Yii::$app->request->post('id');
                $state = Yii::$app->request->post('state');
                if (!$id) {
                    return $this->getTransport()->responseMessage('id is required');
                }
                $object = $this->_getStateMachineObject($id);
                if (!$object) {
                    \Yii::$app->response->setStatusCode(404);
                    return $this->getTransport()->responseMessage('Object not found');
                }
                if ($object->transition($state, ['user' => $this->getUser()])) {
                    return $this->getTransport()->responseScalar();
                } else {
                    return $this->getTransport()->responseMessage($object->getStateMachineError());
                }
            default: throw new HttpException(404);
        }

    }

    public function actionStateList()
    {
        return $this->getTransport()->responseObject($this->_getStateMachineObject()->getApiStateList());
    }

    /**
     * @param int $id primary key
     * @return StateMachineModel
     */
    private function _getStateMachineObject($id = null)
    {
        $model = $this->getModelClass();
        if ($id) {
            /** @var StateMachineModel $object */
            $object = $model::findOne($id);
            if (!$object) {
                return null;
            }
            return $object;
        }
        return new $model;
    }
}