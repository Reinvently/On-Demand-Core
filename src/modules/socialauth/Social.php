<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\socialauth;

/**
 * Class Social
 * @package OAuthClient
 */
class Social
{

    /** @var Provider */
    private $_provider;

    /** @var User */
    private $_user;

    /* -------------------------------------------------------------------------------------------------------------- */

    /**
     * @param Provider $provider
     */
    public function __construct(Provider $provider)
    {
        $this->_provider = $provider;
    }

    /**
     * @param string $requestType
     * @return bool
     */
    public function init($requestType = 'curl')
    {
        $response = $this->_request($this->_provider->url, $requestType);
        $response = $this->_provider->checkResponseError($response);
        if ($response) {
            $this->_user = $this->_provider->getUserDetails($response);
        }
        return $this->_provider->init;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->_provider->getErrors();
    }

    /**
     * @param null $property
     * @return User
     */
    public final function getUser($property = null)
    {
        if ($property) {
            return (isset($this->_user->{$property}) ? $this->_user->{$property} : false);
        } else {
            return $this->_user;
        }
    }

    /* -------------------------------------------------------------------------------------------------------------- */

    /**
     * @param string $url
     * @param string $type
     * @return array|mixed
     */
    private function _request($url, $type)
    {
        try {
            switch ($type) {
                case 'curl':
                    $response = $this->_curlRequest($url, $this->_provider->header);
                    break;
                case 'default':
                    $response = $this->_defaultRequest($url);
                    break;
                default:
                    $this->_provider->setError('Not implemented request type');
                    $response = false;
                    break;
            }
            return $this->_toArray($response);
        } catch (\Exception $error) {
            $this->_provider->setError($error->getMessage(), $error->getCode(), $error->getLine());
        }
        return false;
    }

    /**
     * @param string $url
     * @param array $header
     * @return mixed
     */
    private function _curlRequest($url, $header = [])
    {
        $request = curl_init();
        $options = [
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_HEADER => false,
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_SSL_VERIFYPEER => false,
        ];
        curl_setopt_array($request, $options);
        $result = curl_exec($request);
        if ($error = curl_error($request)) {
            $this->_provider->setError($error);
        }
        curl_close($request);
        return $result;
    }

    /**
     * @param string $url
     * @return mixed
     */
    private function _defaultRequest($url)
    {
        return file_get_contents($url);
    }

    /**
     * @param string $response
     * @return mixed
     */
    private function _toArray($response)
    {
        return json_decode($response, true);
    }
}

