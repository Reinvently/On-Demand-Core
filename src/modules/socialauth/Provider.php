<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\socialauth;

use Yii;

/**
 * Class Provider
 * @package OAuthClient
 */
abstract class Provider
{

    /** @var null|string */
    public $token = null;

    /** @var string */
    public $url = '';

    /** @var bool */
    public $init = true;

    /** @var array */
    public $header = [];

    /* -------------------------------------------------------------------------------------------------------------- */

    /** @var User */
    protected $user;

    /* -------------------------------------------------------------------------------------------------------------- */

    /** @var array */
    private $_errors = [];

    /* -------------------------------------------------------------------------------------------------------------- */

    /**
     * @param string $token
     */
    public function __construct($token)
    {
        $this->token = $token;
        $this->url .= $token;
        $userClass = Yii::$app->user->identityClass;
        $this->user = new $userClass;
    }

    /**
     * @param array $response
     * @return mixed
     */
    public abstract function checkResponseError($response);

    /**
     * @param array $data
     * @return User
     */
    public abstract function getUserDetails($data);

    /**
     * @param string $message
     * @param int $code
     * @param int $line
     */
    public function setError($message, $code = null, $line = null)
    {
        $this->_errors[] = [
            'code' => $code,
            'line' => $line,
            'message' => $message
        ];
        $this->init = false;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }
}
