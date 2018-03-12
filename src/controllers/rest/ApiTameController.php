<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\controllers\rest;


abstract class ApiTameController extends ApiController
{
    public $modelClass = '';

    protected function verbs()
    {
        return [];
    }

    public function actions()
    {
        return [];
    }

} 