<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */
/**
 * Created by PhpStorm.
 * User: sglushko
 * Date: 29.09.2015
 * Time: 13:56
 */

namespace reinvently\ondemand\core\controllers\rest;


use Yii;

abstract class ApiController extends \yii\rest\ActiveController
{

    const JSON = 'on_demand_json';

    const UPDATE_SCENARIO = 'api/update';
    const CREATE_SCENARIO = 'api/create';

    public $serializer = Serializer::class;

    //public $updateScenario = self::UPDATE_SCENARIO;
    //public $createScenario = self::CREATE_SCENARIO;

    public function init()
    {
        parent::init();
        \Yii::$app->getResponse()->format = self::JSON;
        \Yii::$app->user->enableSession = false;
    }

    protected function allowedRoutes()
    {
        return [];
    }

    public function behaviors()
    {
        return [
            'authenticator' => [
                'class' => HttpBearerAuth::className(),
                'except' => $this->allowedRoutes(),
            ],
        ];
    }

    public function actions()
    {
        $actions = parent::actions();
        $actions['search'] = [
            'class' => SearchAction::class,
            'modelClass' => $this->modelClass,
            'checkAccess' => [$this, 'checkAccess'],
            'params' => \Yii::$app->request->get()
        ];

        return $actions;
    }

    protected function verbs()
    {
        $verbs = parent::verbs();
        $verbs['search'] = ['GET'];

        return $verbs;
    }

    public function beforeAction($action)
    {
        return parent::beforeAction($action);
    }

    public function afterAction($action, $result)
    {
        /** @var mixed $result */
        $result = \yii\base\Controller::afterAction($action, $result);

        /** @var Serializer $serializer */
        $serializer = Yii::createObject($this->serializer);
        $serializer->setAction($action);

//        $result->setPagination(['pageSize' => 5]);

        return $serializer->serialize($result);
    }

    public function getTransport()
    {
        return Yii::$app->transport;
    }

}