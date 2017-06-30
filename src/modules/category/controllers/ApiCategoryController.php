<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\modules\category\controllers;


use reinvently\ondemand\core\modules\category\models\Category;

class ApiCategoryController extends \reinvently\ondemand\core\controllers\rest\ApiController
{
    public $modelClass = Category::class;

}