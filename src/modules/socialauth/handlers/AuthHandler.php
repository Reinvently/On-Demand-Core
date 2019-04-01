<?php
/**
 * @copyright Reinvently (c) 2018
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\modules\socialauth\handlers;


use reinvently\ondemand\core\exceptions\LogicException;
use reinvently\ondemand\core\exceptions\UserException;
use reinvently\ondemand\core\modules\socialauth\models\Auth;
use reinvently\ondemand\core\modules\user\models\User;
use Yii;
use yii\authclient\ClientInterface;
use yii\helpers\ArrayHelper;

/**
 * AuthHandler handles successful authentication via Yii auth component
 */
class AuthHandler
{
    /**
     * @var ClientInterface
     */
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function handle()
    {
        $attributes = $this->client->getUserAttributes();
        $email = ArrayHelper::getValue($attributes, 'email');
        $id = ArrayHelper::getValue($attributes, 'id');
        $firstName = ArrayHelper::getValue($attributes, 'first_name');
        $lastName = ArrayHelper::getValue($attributes, 'last_name');

        /* @var Auth $auth */
        $auth = Auth::find()->where([
            'source' => $this->client->getId(),
            'sourceId' => $id,
        ])->one();

        if (!Yii::$app->user) {
            throw new LogicException(
                Yii::t('app',
                    'app->user is empty'
                )
            );
        }

        if (Yii::$app->user->isGuest) {
            if ($auth) { // login
                /* @var User $user */
                $user = $auth->user;
//                $this->updateUserInfo($user);
                Yii::$app->user->login($user);
            } else { // signup
                if ($email !== null && User::find()->where(['email' => $email])->exists()) {
                    throw new UserException(
                        Yii::t('app',
                            'User with the same email as in {client} account already exists but isn\'t linked to it. Login using email first to link it.',
                            ['client' => $this->client->getTitle()]
                        )
                    );
                } else {
                    $password = Yii::$app->security->generateRandomString(6);
//                    needs to find user by uuid
                    $user = new User([
                        'firstName' => $firstName,
                        'lastName' => $lastName,
                        'email' => $email,
                        'password' => $password,
                    ]);
                    $user->generateClientWithAuthKey();

                    $transaction = User::getDb()->beginTransaction();

                    if ($user->save()) {
                        $auth = new Auth([
                            'userId' => $user->id,
                            'source' => $this->client->getId(),
                            'sourceId' => (string)$id,
                        ]);
                        if ($auth->save()) {
                            $transaction->commit();
                            Yii::$app->user->login($user);
                        } else {
                            throw new UserException(
                                Yii::t('app', 'Unable to save {client} account: {errors}', [
                                    'client' => $this->client->getTitle(),
                                    'errors' => json_encode($auth->getErrors()),
                                ])
                            );
                        }
                    } else {
                        throw new UserException(
                            Yii::t('app', 'Unable to save user: {errors}', [
                                'client' => $this->client->getTitle(),
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
                    'source' => $this->client->getId(),
                    'sourceId' => (string)$attributes['id'],
                ]);
                if ($auth->save()) {
                    /** @var User $user */
                    $user = $auth->user;
//                    $this->updateUserInfo($user);
                    throw new UserException(
                        Yii::t('app', 'Linked {client} account.', [
                            'client' => $this->client->getTitle()
                        ])
                    );
                } else {
                    throw new UserException(
                        Yii::t('app', 'Unable to link {client} account: {errors}', [
                            'client' => $this->client->getTitle(),
                            'errors' => json_encode($auth->getErrors()),
                        ])
                    );
                }
            } else { // there's existing auth
                throw new UserException(
                    Yii::t('app',
                        'Unable to link {client} account. There is another user using it.',
                        ['client' => $this->client->getTitle()])
                );
            }
        }
    }

    /**
     * @param User $user
     */
    private function updateUserInfo(User $user)
    {
        $user->save();
    }
}