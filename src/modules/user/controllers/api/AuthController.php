<?php
/**
 * @copyright Reinvently (c) 2018
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\user\controllers\api;

use reinvently\ondemand\core\controllers\rest\ApiTameController;
use reinvently\ondemand\core\exceptions\LogicException;
use reinvently\ondemand\core\modules\role\models\Role;
use reinvently\ondemand\core\modules\user\models\Client;
use Yii;
use reinvently\ondemand\core\modules\user\models\AuthModel;
use reinvently\ondemand\core\modules\user\models\User;
use yii\helpers\ArrayHelper;

class AuthController extends ApiTameController
{
    /** @var Role */
    public $roleModelClass = Role::class;

    protected function allowedRoutes()
    {
        return [
            'login',
            'guest',
            'register',
        ];
    }

    public function behaviors()
    {
        $verbs = [
            'verbs' => [
                'class' => \yii\filters\VerbFilter::class,
                'actions' => [
                    'login' => ['post'],
                    'guest' => ['post'],
                    'logout' => ['post'],
                    'register' => ['post'],
                ]
            ],
        ];
        return ArrayHelper::merge($verbs, parent::behaviors());
    }

    public function actionLogin()
    {
        $uuid = Yii::$app->request->post('uuid', self::getSessionId());
        $data = [
            'username' => Yii::$app->request->post('username'),
            'password' => Yii::$app->request->post('password'),
        ];

        /** @var AuthModel $model */
        $model = new AuthModel();
        if ($model->load(['AuthModel' => $data]) and $model->login()) {

            /** @var Client $client */
            $client = $model->getUser()->generateClientWithAuthKey($uuid);

            return $this->getTransport()->responseObject([
                'uuid' => $client->uuid,
                'token' => $client->token,
                'id' => $client->userId
            ]);

        } else {
            return $model;
        }
    }

    public function actionGuest()
    {
        $uuid = Yii::$app->request->post('uuid', self::getSessionId());


        /** @var Client $client */
        $client = Client::findActive($uuid);
        if (!$client) {
            // Create new guest User
            $userClass = Yii::$app->user->identityClass;
            /** @var User $user */
            $user = $userClass::createGuest();

            /** @var Client $client */
            $client = $user->generateClientWithAuthKey($uuid);
        }

        $roleModelClass = $this->roleModelClass;
        if ($client->user->roleId != $roleModelClass::GUEST) {
            return $this->getTransport()->responseMessage('You must use login & password to connect using this client');
        }

        return $this->getTransport()->responseObject([
            'uuid' => $client->uuid,
            'token' => $client->token,
            'id' => $client->userId
        ]);
    }

    public function actionLogout()
    {
        Client::deleteAll(['token' => Yii::$app->user->identity->getAuthKey()]);

        return $this->getTransport()->responseScalar();
    }

    public function actionRegister()
    {
        $post = Yii::$app->request->post();

        $userClass = Yii::$app->user->identityClass;
        /** @var User $user */
        $user = new $userClass;
        $user->setAttributes($post);
        $roleModelClass = $this->roleModelClass;
        $user->roleId = $roleModelClass::USER;
        if (!$user->save()) {
            return $user;
        }

        return $this->getTransport()->responseScalar();
    }

    public static function getSessionId()
    {
        $session = Yii::$app->session;
        $session->open();
        return $session->getId();
    }

}