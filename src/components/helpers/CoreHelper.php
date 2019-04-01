<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\components\helpers;
use reinvently\ondemand\core\components\loggers\models\ExceptionLog;
use reinvently\ondemand\core\exceptions\LogicException;
use yii\base\InvalidArgumentException;
use yii\helpers\Json;

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


    /**
     * @param string $type
     * @param mixed $value
     * @return bool|float|int|null|string
     * @throws LogicException
     */
    public static function apiTypeConversion($type, $value) {
        if ($value === null) {
            return null;
        }

        switch ($type) {
            case 'int':
            case 'integer':
                return (int) $value;
            case 'bool':
            case 'boolean':
                return (bool) $value;
            case 'float':
            case 'double':
            case 'real':
                return (double) $value;
            case 'string':
                return (string) $value;
        }

        throw new LogicException('undefined type: ' . $type);
    }

    /**
     * @return string
     */
    public static function getEnvironment()
    {
        return require(\Yii::getAlias('@app') . '/environment.php');
    }

    /**
     * @param string $curlResponse
     * @return array|null
     */
    static public function jsonSaveDecode($curlResponse) {
        try {
            return Json::decode($curlResponse);
        } catch (InvalidArgumentException $e) {
            ExceptionLog::saveException(new \Exception('Invalid JSON. Response: ' . $curlResponse));
            ExceptionLog::saveException($e);
        }

        return null;
    }

}