<?php
/**
 * @copyright Reinvently (c) 2018
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\modules\servicearea\controllers;


use reinvently\ondemand\core\controllers\rest\ApiController;
use reinvently\ondemand\core\modules\servicearea\models\Zip;

class ApiZipController extends ApiController
{
    public $modelClass = Zip::class;
}