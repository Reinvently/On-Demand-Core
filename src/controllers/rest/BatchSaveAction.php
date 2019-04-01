<?php
/**
 * @copyright Reinvently (c) 2018
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\controllers\rest;

use Yii;
use yii\db\ActiveRecord;
use yii\web\ServerErrorHttpException;

class BatchSaveAction extends \fproject\rest\BatchSaveAction
{
    /**
     * @inheritDoc
     */
    public function run()
    {
        $modelArr = Yii::$app->getRequest()->getBodyParams();

        $models = [];
        foreach ($modelArr as $index => $m) {
            /* @var $model ActiveRecord */
            $model = new $this->modelClass([
                'scenario' => $this->scenario,
            ]);

            if (!$this->loadModel($model, $m)) {
                throw new ServerErrorHttpException('Failed to batch-save the models: invalid data');
            }

            if (
                $model->getPrimaryKey()
                && $model->refresh()
                && $this->loadModel($model, $m)
            ) {
                if (!$this->loadModel($model, $m)) {
                    throw new ServerErrorHttpException('Failed to batch-save the models: invalid data');
                }
            }

            if ($this->checkAccess) {
                call_user_func($this->checkAccess, $this->id, $model);
            }

            $models[$index] = $model;
        }


        /** @var ApiController $controller */
        $controller = $this->controller;
        /** @var Serializer $serializer */
        $serializer = Yii::createObject($controller->serializer);
        $serializer->setAction($this);

        $response = [];
        foreach ($models as $index => $model) {
            $model->save();

            \Yii::$app->getResponse()->setStatusCode(200);
            $response[$index] = $serializer->serialize($model);
        }
        \Yii::$app->getResponse()->setStatusCode(200);
        return $response;
    }

}