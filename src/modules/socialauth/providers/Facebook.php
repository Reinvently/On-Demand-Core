<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\socialauth\providers;


/**
 * Class Facebook
 * @package OAuthClient
 */
class Facebook extends \reinvently\ondemand\core\modules\socialauth\Provider
{

    /** @var string */
    public $url = 'https://graph.facebook.com/me?access_token=';

    /* -------------------------------------------------------------------------------------------------------------- */

    /** @var User */
    protected $user;

    /* -------------------------------------------------------------------------------------------------------------- */

    /**
     * @param string $token
     */
    public function __construct($token)
    {
        parent::__construct($token);
    }


    /**
     * @param array $response
     * @return bool
     */
    public function checkResponseError($response)
    {
        if (!$response) {
            $this->setError('Request error');
        } elseif (isset($response['error']) and isset($response['error']['message'])) {
            $this->setError($response['error']['message'], $response['error']['code']);
            $response = false;
        }
        return $response;
    }


    /**
     * @param array $data
     * @return User
     */
    public function getUserDetails($data)
    {
        try {
            $this->user->facebookId = $data['id'];
            $this->user->firstName = (isset($data['first_name']) ? $data['first_name'] : null);
            $this->user->lastName = (isset($data['last_name']) ? $data['last_name'] : null);
            $this->user->email = (isset($data['email']) ? $data['email'] : null);
            /*$this->user->gender = (isset($data['gender']) ? $data['gender'] : null);
            $json = file_get_contents('https://graph.facebook.com/me/picture?redirect=false&width=200&height=200&access_token=' . $this->token);
            if ($json and $json = \CJSON::decode($json) and isset($json['data']) and isset($json['data']['is_silhouette']) and !$json['data']['is_silhouette']) {
                $this->user->photo = $json['data']['url'];
            } else {
                $this->user->photo = null;
            }*/
        } catch (\Exception $error) {
            $this->setError($error->getMessage(), $error->getCode(), $error->getLine());
        }
        return $this->user;
    }
}
