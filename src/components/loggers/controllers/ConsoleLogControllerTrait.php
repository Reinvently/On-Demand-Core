<?php
/**
 * @copyright Reinvently (c) 2018
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\components\loggers\controllers;

use reinvently\ondemand\core\components\loggers\models\ConsoleLog;
use reinvently\ondemand\core\controllers\console\ConsoleController;
use Yii;

/**
 * Trait ConsoleLogControllerTrait
 * @package reinvently\ondemand\core\components\loggers\controllers
 *
 * @mixin ConsoleController
 */
trait ConsoleLogControllerTrait
{
    /** @var  ConsoleLog */
    private $consoleLog;

    public function beforeAction($action)
    {
        $this->saveLogRequest();

        return parent::beforeAction($action);
    }

    public function afterAction($action, $result)
    {
        $result = parent::afterAction($action, $result);
        $this->saveLogResponse();

        return $result;
    }

    /**
     * @return bool $result
     */
    public function saveLogRequest()
    {
        $this->consoleLog = new ConsoleLog();
        $this->consoleLog->startedAt = time();
        $this->consoleLog->route = $this->route;
        $this->consoleLog->generateHtmlRequestParams(Yii::$app->request->getParams());

        return $this->consoleLog->save();
    }

    /**
     * @return bool $result
     */
    public function saveLogResponse()
    {
        if (!$this->consoleLog) {
            return false;
        }

        $this->consoleLog->finishedAt = time();
        if (\Yii::$app->has('response')) {
            $this->consoleLog->generateHtmlResponseStatusCode(\Yii::$app->getResponse());
        }

        return $this->consoleLog->save();
    }

}
