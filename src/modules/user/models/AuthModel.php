<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\user\models;

use Yii;
use \yii\base\Model;

class AuthModel extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $user;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    public function login()
    {
        if ($this->validate()) {
            //$this->getUser()->generateAuthKey();
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 30*24*60*60 : 0);
        }
        return false;
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            /** @var User $user */
            $user = $this->getUser();
            if (!$user or !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password');
            }
        }
    }

    /**
     * @return null|User
     */
    public function getUser()
    {
        if ($this->user === null) {
            /** @var User $userClass */
            $userClass = \Yii::$app->user->identityClass;
            return $userClass::findByUsername($this->username);
        }
        return $this->user;
    }


}