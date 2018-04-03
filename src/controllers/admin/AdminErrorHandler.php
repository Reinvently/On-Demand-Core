<?php
/**
 * @copyright Reinvently (c) 2018
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\controllers\admin;

use reinvently\ondemand\core\components\loggers\models\ExceptionLog;
use yii\web\ErrorHandler;

class AdminErrorHandler extends ErrorHandler
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