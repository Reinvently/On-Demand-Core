<?php
/**
 * @copyright Reinvently (c) 2018
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

/**
 * Created by PhpStorm.
 * User: sglushko
 * Date: 02.04.2018
 * Time: 15:34
 */


namespace reinvently\ondemand\core\controllers\console;


use reinvently\ondemand\core\components\loggers\controllers\ConsoleLogControllerTrait;
use yii\console\Controller;

class ConsoleController extends Controller
{
    use ConsoleLogControllerTrait;

}