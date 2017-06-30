<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\modules\settings\controllers;

use reinvently\ondemand\core\modules\settings\models\Tariff;

class ApiTariffController extends \reinvently\ondemand\core\controllers\rest\ApiController
{
    public $modelClass = Tariff::class;

}