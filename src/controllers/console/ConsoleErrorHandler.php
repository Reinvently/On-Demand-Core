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
 * Time: 22:40
 */


namespace reinvently\ondemand\core\controllers\console;


use reinvently\ondemand\core\components\loggers\models\ExceptionLog;
use yii\console\ErrorHandler;

class ConsoleErrorHandler extends ErrorHandler
{
    /**
     * @param \Exception $exception
     */
    protected function renderException($exception)
    {
        ExceptionLog::saveException($exception, true);
        parent::renderException($exception);
    }

}