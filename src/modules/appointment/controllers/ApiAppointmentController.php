<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\modules\appointment\controllers;


use reinvently\ondemand\core\controllers\rest\ApiController;
use reinvently\ondemand\core\modules\appointment\models\Appointment;

class ApiAppointmentController extends ApiController
{
    public $modelClass = Appointment::class;
} 