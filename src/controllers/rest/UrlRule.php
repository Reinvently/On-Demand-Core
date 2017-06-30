<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\controllers\rest;


class UrlRule extends \yii\rest\UrlRule
{
    public $tokens = [
        '{id}' => '<id:\\w[\\w,]*>',
    ];

}