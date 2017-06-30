<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\components\helpers;

/**
 * Class CoreHelper
 * @package reinvently\ondemand\core\components\helpers
 */
class CoreHelper
{
    /**
     * @param $url
     * @param $token
     * @param string $method
     * @return mixed
     */
    public static function ApiRequest($url, $token, $method = 'GET')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $token]);
        //curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:30.0) Gecko/20100101 Firefox/30.0');
        $output = \yii\helpers\Json::decode(curl_exec($ch));
        curl_close($ch);
        return $output;
    }

    /**
     * @static
     * @param int $length
     * @return string
     */
    public static function randomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $string = '';
        for ($p = 0; $p < $length; $p++) {
            $string .= $characters[mt_rand(0, strlen($characters) - 1)];
        }
        return $string;
    }
}