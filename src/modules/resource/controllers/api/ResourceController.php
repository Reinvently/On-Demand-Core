<?php
/**
 * @copyright Reinvently (c) 2018
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\resource\controllers\api;


use reinvently\ondemand\core\controllers\rest\ApiController;
use reinvently\ondemand\core\modules\resource\models\Resource;

class ResourceController extends ApiController
{
    public $modelClass = Resource::class;
}