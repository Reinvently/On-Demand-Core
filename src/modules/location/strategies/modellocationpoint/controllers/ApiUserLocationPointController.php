<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\modules\location\strategies\modellocationpoint\controllers;


use reinvently\ondemand\core\modules\location\controllers\ApiPointController;
use reinvently\ondemand\core\modules\location\strategies\modellocationpoint\models\UserLocationPoint;

class ApiUserLocationPointController extends ApiPointController
{
    public $modelClass = UserLocationPoint::class;

}