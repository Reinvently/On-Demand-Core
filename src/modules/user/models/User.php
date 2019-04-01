<?php
/**
 * @copyright Reinvently (c) 2018
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\user\models;

use reinvently\ondemand\core\components\model\CoreModel;
use reinvently\ondemand\core\components\transport\ApiInterface;
use reinvently\ondemand\core\components\transport\ApiTransportTrait;
use reinvently\ondemand\core\exceptions\LogicException;
use reinvently\ondemand\core\modules\role\models\Role;
use reinvently\ondemand\core\modules\socialauth\models\Auth;
use Yii;
use yii\base\Exception;
use yii\db\ActiveQuery;
use yii\web\IdentityInterface;

/**
 * Class User
 *
 * @property int $id
 * @property string $email
 * @property string $password
 * @property int $roleId
 * @property string $authKey
 * @property string $firstName
 * @property string $lastName
 * @property string $phone
 * @property int $createdAt
 * @property int $updatedAt
 * @property string $language
 *
 * @property User identity
 *
 * @method User getIdentity(bool $autoRenew)
 *
 * @property Auth[] auths
 */
class User extends CoreModel implements IdentityInterface, ApiInterface
{
    use ApiTransportTrait;

    const SCENARIO_GUEST = 'guest';

    /** @var Client */
    public static $clientModelClass = Client::class;

    /** @var Role */
    public static $RoleModelClass = Role::class;

    /** @var Client */
    public $currentClient;

    /**
     * Finds an identity by the given ID.
     * @param string|integer $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        return self::findOne($id);
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $client = static::$clientModelClass;
        /** @var Client $client */
        $client = $client::find()
            ->where(['token' => $token])
            ->andWhere(['>=', 'expiredAt', time()])
            ->one();
        if ($client && $client->user) {
            $client->user->currentClient = $client;
            return $client->user;
        }
        return null;
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|integer an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        if ($this->currentClient) {
            return $this->currentClient->token;
        }
        return null;
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return boolean whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        return $authKey == $this->authKey ? true : false;
    }

    public static function tableName()
    {
        return 'user';
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['email', 'phone', 'password', 'roleId'], 'required'],
            ['email', 'email'],
            ['email', 'unique'],
            ['phone', 'unique'],
            ['password', 'string', 'min' => 6],
            [['firstName', 'lastName', 'phone'], 'string', 'max' => 25],
            [['language'], 'string'],
            [['firstName', 'lastName'], 'safe'],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_GUEST] = $scenarios[self::SCENARIO_DEFAULT];
        foreach (['email', 'password', 'phone'] as $v) {
            $key = array_search($v, $scenarios[self::SCENARIO_GUEST]);
            unset($scenarios[self::SCENARIO_GUEST][$key]);
        }
        return $scenarios;
    }

    /**
     * @return ActiveQuery
     */
    public function getAuths()
    {
        return $this->hasMany(Auth::className(), ['userId' => 'id']);
    }

    public function attributeLabels()
    {
        return [
            'roleId' => 'Role',
        ];
    }

    public static function findByUsername($username)
    {
        if (!$username) {
            return null;
        }

        return static::find()
            ->orWhere(['email' => $username])
            ->orWhere(['phone' => $username])
            ->one();
    }

    public function validatePassword($password)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $this->password);
    }

    public function beforeSave($insert)
    {
        if ($this->getIsNewRecord()) {
            $this->createdAt = time();
        }
        if (!empty($this->getDirtyAttributes(['password']))) {
            $this->password = Yii::$app->getSecurity()->generatePasswordHash($this->password);
        }
        if ($this->getDirtyAttributes()) {
            $this->updatedAt = time();
        }
        return parent::beforeSave($insert);
    }

    public function beforeValidate()
    {
        if (!$this->roleId) {
            $this->roleId = Role::USER;
        }

        return parent::beforeValidate();
    }

    /**
     * @param $uuid
     * @return Client
     * @throws LogicException
     */
    public function generateClientWithAuthKey($uuid = null)
    {
        if ($this->getIsNewRecord()) {
            throw new LogicException('User must be saved in database');
        }

        $client = null;
        if ($uuid) {
            $client = Client::findActive($uuid, $this->id);
        } else {
            $uuid = Yii::$app->getSecurity()->generateRandomString(32);
        }
        if (!$client) {
            // Create new Client
            /** @var Client $client */
            $client = new Client();
            $client->userId = $this->id;
            $client->uuid = $uuid;
            $client->token = $client->generateToken();
            $client->ip = Yii::$app->request->userIP;
            if (!$client->save()) {
                throw new LogicException('Client not saved');
            }
        }

        return $client;
    }

    /**
     * @return array
     */
    public function getItemForApi()
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'phone' => $this->phone,
            'language' => $this->language,
        ];
    }

    /**
     * @return array
     */
    public function getItemShortForApi()
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'phone' => $this->phone,
        ];
    }

    /**
     * @return User
     * @throws Exception
     */
    public static function createGuest()
    {
        $user = new static();
        $user->setScenario($user::SCENARIO_GUEST);
        if (!$user->save()) {
            throw new Exception('Cannot create guest user');
        }
        return $user;
    }

    public function getName()
    {
        if (!$this->firstName and !$this->lastName)
            return false;
        return $this->firstName . ' ' . $this->lastName;
    }

}