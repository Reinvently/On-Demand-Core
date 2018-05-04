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


use reinvently\ondemand\core\components\loggers\controllers\ApiLogControllerTrait;
use reinvently\ondemand\core\components\model\CoreModel;
use reinvently\ondemand\core\modules\role\models\Role;
use reinvently\ondemand\core\modules\user\models\User;
use Yii;
use yii\base\Controller;
use yii\rest\ActiveController;
use yii\web\ForbiddenHttpException;

/**
 * Class ApiController
 * @package reinvently\ondemand\core\controllers\rest
 */
abstract class ApiController extends ActiveController
{
    use ApiLogControllerTrait;

    const JSON = 'on_demand_json';

    const UPDATE_SCENARIO = 'api/update';
    const CREATE_SCENARIO = 'api/create';

    public $serializer = Serializer::class;

    public $updateScenario = self::UPDATE_SCENARIO;
    public $createScenario = self::CREATE_SCENARIO;

    public function init()
    {
        parent::init();
        Yii::$app->getResponse()->format = self::JSON;
        Yii::$app->user->enableSession = false;
    }

    protected function allowedRoutes()
    {
        return [];
    }

    public function behaviors()
    {
        return [
            'authenticator' => [
                'class' => HttpBearerAuth::class,
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
            'params' => Yii::$app->request->get()
        ];

        return $actions;
    }

    protected function verbs()
    {
        $verbs = parent::verbs();
        $verbs['search'] = ['GET'];

        return $verbs;
    }

    protected function setLanguage()
    {

    }

    public function beforeAction($action)
    {
        $result = parent::beforeAction($action);

        $user = $this->getUser();
        if ($user && $user->language) {
            \Yii::$app->language = $user->language;
        }

        $this->saveLogRequest();

        return $result;
    }

    /**
     * @param \yii\base\Action $action
     * @param mixed $result
     * @return mixed
     */
    public function afterAction($action, $result)
    {
        /** @var mixed $result */
        $result = Controller::afterAction($action, $result);

        $this->saveLogResponse();

        /** @var Serializer $serializer */
        $serializer = Yii::createObject($this->serializer);
        $serializer->setAction($action);

//        $result->setPagination(['pageSize' => 5]);

        $result = $serializer->serialize($result);

        return $result;
    }

    /**
     * @return \reinvently\ondemand\core\components\transport\TransportInterface
     */
    public function getTransport()
    {
        return Yii::$app->transport;
    }

    /**
     * @param bool $autoRenew
     * @return User
     */
    public function getUser($autoRenew = false)
    {
        return Yii::$app->user->getIdentity($autoRenew);
    }

    public function fullAccessActions() {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function checkAccess($action, $model = null, $params = [])
    {
        if (in_array($action, $this->fullAccessActions())) {
            return;
        }

        /** @var User $user */
        $user = Yii::$app->getUser()->identity;

        if ($user->roleId == Role::ADMIN) {
            return;
        }

        if (!$model) {
            throw new ForbiddenHttpException();
        }

        $userId = null;
        /** @var CoreModel $model */
        if ($model instanceof User) {
            $userId = $model->id;
        } elseif ($model->hasAttribute('userId')) {
            $userId = $model->userId;
        } else {
            return;
        }

        if ($userId == Yii::$app->getUser()->id) {
            return;
        }

        throw new ForbiddenHttpException();
    }

}