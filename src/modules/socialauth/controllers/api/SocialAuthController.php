<?php
/**
 * @copyright Reinvently (c) 2018
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\modules\socialauth\controllers\api;


use reinvently\ondemand\core\controllers\rest\ApiTameController;
use reinvently\ondemand\core\exceptions\LogicException;
use reinvently\ondemand\core\exceptions\UserException;
use reinvently\ondemand\core\modules\socialauth\models\Auth;
use reinvently\ondemand\core\modules\user\models\Client;
use reinvently\ondemand\core\modules\user\models\User;
use Yii;
use yii\authclient\BaseClient;
use yii\authclient\clients\Facebook;
use yii\authclient\OAuthToken;
use yii\helpers\ArrayHelper;

class SocialAuthController extends ApiTameController
{
    protected function allowedRoutes()
    {
        return [
            'login',
        ];
    }

    public function behaviors()
    {
        $verbs = [
            'verbs' => [
                'class' => \yii\filters\VerbFilter::class,
                'actions' => [
                    'login' => ['post'],
                ]
            ],
        ];
        return ArrayHelper::merge($verbs, parent::behaviors());
    }

    public function actionLogin()
    {
//        $uuid = Yii::$app->request->post('uuid', self::getSessionId());

        $token = Yii::$app->request->post('token');
        $source = Yii::$app->request->post('source');

        $accessToken = new OAuthToken();
        $accessToken->token = $token;

        // todo not only facebook
        /** @var Facebook $client */
        $client = \Yii::$app->authClientCollection->getClient($source);

        $client->accessToken = $accessToken;

        return $this->handle($client);

    }

//    public static function getSessionId()
//    {
//        $session = Yii::$app->session;
//        $session->open();
//        return $session->getId();
//    }

    /**
     * @param BaseClient $client
     * @return mixed
     * @throws LogicException
     * @throws UserException
     */
    public function handle($client)
    {
        $attributes = $client->getUserAttributes();
        $email = ArrayHelper::getValue($attributes, 'email');
        $id = ArrayHelper::getValue($attributes, 'id');

        /* @var Auth $auth */
        $auth = Auth::find()->where([
            'source' => $client->getId(),
            'sourceId' => $id,
        ])->one();

        if (!Yii::$app->user) {
            throw new LogicException(
                Yii::t('app',
                    'app->user is empty'
                )
            );
        }

        /** @var User $userClass */
        $userClass = Yii::$app->user->identityClass;

        if (Yii::$app->user->isGuest) {
            if ($auth && $auth->user) { // login
                /* @var User $user */
                $user = $auth->user;

                return $this->login($user);

            } else { // signup
                if ($email !== null && $userClass::find()->where(['email' => $email])->exists()) {
                    throw new UserException(
                        Yii::t('app',
                            'User with the same email as in {client} account already exists but isn\'t linked to it. Login using email first to link it.',
                            ['client' => $client->getTitle()]
                        )
                    );
                } else {
                    $password = Yii::$app->security->generateRandomString(6);
//                    needs to find user by uuid

                    /** @var User $user */
                    $user = new $userClass([
                        'email' => $email,
                        'password' => $password,
                    ]);
                    $this->loadUserInfo($user, $client);
                    $transaction = $userClass::getDb()->beginTransaction();

                    if ($user->save()) {
                        $auth = new Auth([
                            'userId' => $user->id,
                            'source' => $client->getId(),
                            'sourceId' => (string)$id,
                        ]);
                        if ($auth->save()) {
                            $transaction->commit();
                            return $this->login($user);
                        } else {
                            throw new UserException(
                                Yii::t('app', 'Unable to save {client} account: {errors}', [
                                    'client' => $client->getTitle(),
                                    'errors' => json_encode($auth->getErrors()),
                                ])
                            );
                        }
                    } else {
                        throw new UserException(
                            Yii::t('app', 'Unable to save user: {errors}', [
                                'client' => $client->getTitle(),
                                'errors' => json_encode($user->getErrors()),
                            ])
                        );
                    }
                }
            }
        } else { // user already logged in
            if (!$auth) { // add auth provider
                $auth = new Auth([
                    'userId' => Yii::$app->user->id,
                    'source' => $client->getId(),
                    'sourceId' => (string)$attributes['id'],
                ]);
                if ($auth->save()) {
                    return $this->login($this->getUser());
                } else {
                    throw new UserException(
                        Yii::t('app', 'Unable to link {client} account: {errors}', [
                            'client' => $client->getTitle(),
                            'errors' => json_encode($auth->getErrors()),
                        ])
                    );
                }
            } else { // there's existing auth
                throw new UserException(
                    Yii::t('app',
                        'Unable to link {client} account. There is another user using it.',
                        ['client' => $client->getTitle()])
                );
            }
        }
    }

    /**
     * @param User $user
     * @param BaseClient $client
     */
    public function loadUserInfo($user, $client)
    {
        $attributes = $client->getUserAttributes();
        $firstName = ArrayHelper::getValue($attributes, 'first_name');
        $lastName = ArrayHelper::getValue($attributes, 'last_name');

        $user->firstName = $firstName;
        $user->lastName = $lastName;
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function login($user)
    {
        if (Yii::$app->user->login($user)) {
            /** @var Client $client */
            $client = $user->generateClientWithAuthKey();

            return $this->getTransport()->responseObject([
                'uuid' => $client->uuid,
                'token' => $client->token,
                'id' => $client->userId
            ]);

        } else {
            return $user;
        }
    }

}