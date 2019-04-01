<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\controllers\rest;


use reinvently\ondemand\core\components\base\ErrorManagerInterface;
use reinvently\ondemand\core\components\transport\ApiInterface;
use reinvently\ondemand\core\components\transport\TransportInterface;
use yii\base\Arrayable;
use yii\base\Model;
use yii\data\DataProviderInterface;
use yii\rest\Action;
use yii\rest\CreateAction;
use yii\rest\DeleteAction;
use yii\rest\IndexAction;
use yii\rest\UpdateAction;
use yii\rest\ViewAction;

class Serializer extends \yii\rest\Serializer
{
    /** @var Action */
    protected $action;

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @return TransportInterface
     */
    protected function getTransport()
    {
        return \Yii::$app->transport;
    }

    /**
     * @param mixed $data
     * @return array|mixed
     */
    public function serialize($data)
    {
        if (($data instanceof Model || $data instanceof ErrorManagerInterface) && $data->hasErrors()) {
            return $this->serializeModelErrors($data);
        } elseif ($data instanceof ApiInterface) {
            return $this->serializeModel($data);
        } elseif ($data instanceof DataProviderInterface) {
            return $this->serializeDataProvider($data);
        } else {
            return $data;
        }
    }


    /**
     * Serializes a model object.
     * @param ApiInterface $model
     * @return array the array representation of the model
     */
    protected function serializeModel($model)
    {
        if ($this->request->getIsHead()) {
            return null;
        } else {
//            list ($fields, $expand) = $this->getRequestedFields();

            $action = $this->getAction();

            if (
                $action instanceof IndexAction
                || $action instanceof SearchAction
            ) {
                return $this->getTransport()->responseItemShort($model);
            } elseif ($action instanceof ViewAction) {
                return $this->getTransport()->responseItem($model);
            } elseif ($action instanceof CreateAction) {
                return $this->getTransport()->responseItemId($model);
            } elseif ($action instanceof BatchSaveAction) {
                return $this->getTransport()->responseItemId($model);
            } elseif (
                $action instanceof DeleteAction
                || $action instanceof UpdateAction
            ) {
                return $this->getTransport()->responseScalar();
            } elseif ($model instanceof ApiInterface) {
                return $this->getTransport()->responseItem($model);
            }

            throw new \LogicException('Undefined Action');
        }
    }

    /**
     * @param array $models
     * @return \reinvently\ondemand\core\vendor\transportjson\ResponseObject
     */
    protected function serializeModels(array $models)
    {
//        list ($fields, $expand) = $this->getRequestedFields();

        $action = $this->getAction();

        /** @var ApiInterface[] $models */

        if (
            $action instanceof IndexAction
            || $action instanceof SearchAction
        ) {
            return $this->getTransport()->responseListShort($models);
        } elseif ($action instanceof ViewAction) {
            return $this->getTransport()->responseList($models);
        } elseif ($action instanceof CreateAction) {
            return $this->getTransport()->responseListIds($models);
        } elseif (
            $action instanceof DeleteAction
            || $action instanceof UpdateAction
        ) {
            return $this->getTransport()->responseScalar();
        }

        return $this->getTransport()->responseListShort($models);
//        throw new \LogicException('Undefined Action');
    }

    protected function serializeModelErrors($model)
    {
        $this->response->setStatusCode(400);
        return $this->getTransport()->responseErrors($model->getErrors());
    }


} 