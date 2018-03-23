<?php
/**
 * @copyright Reinvently (c) 2018
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

/**
 * Created by PhpStorm.
 * User: sglushko
 * Date: 23.03.2018
 * Time: 14:32
 */
namespace reinvently\ondemand\core\modules\setting\controllers;


use reinvently\ondemand\core\controllers\rest\ApiController;
use reinvently\ondemand\core\modules\setting\models\Setting;

class SettingController extends ApiController
{
    public $modelClass = Setting::class;
}