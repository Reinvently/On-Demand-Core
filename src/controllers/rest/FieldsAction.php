<?php
/**
 * Created by PhpStorm.
 * User: sglushko
 * Date: 18.11.2018
 * Time: 22:49
 */

namespace reinvently\ondemand\core\controllers\rest;


use reinvently\ondemand\core\components\model\CoreModel;
use yii\base\Model;
use yii\rest\Action;

class FieldsAction extends Action
{
    /**
     * @var string the scenario to be assigned to the new model before it is validated and saved.
     */
    public $scenario = Model::SCENARIO_DEFAULT;

    /**
     *
     * @return mixed
     * @throws ServerErrorHttpException if there is any error when creating the model
     */
    public function run()
    {
        /** @var CoreModel $model */
        $model = new $this->modelClass;
        return \Yii::$app->transport->responseObject([
            'safe' => $model->safeAttributes(),
            'unsafe' => $model->unsafeAttributes()
        ]);
    }
}