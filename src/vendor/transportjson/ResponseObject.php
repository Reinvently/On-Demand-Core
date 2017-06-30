<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\vendor\transportjson;

/**
 * Class ResponseObject
 * @package reinvently\ondemand\core\vendor\transportjson
 * @property bool success
 * @property int type
 * @property mixed data
 * @property object[] errors
 * @property string message
 * @property object debug
 */
class ResponseObject
{
    const TYPE_ITEM = 1;
    const TYPE_LIST = 2;

    public $success;
    public $type;
    public $data;
    public $errors;
    public $message;
    public $debug;
}