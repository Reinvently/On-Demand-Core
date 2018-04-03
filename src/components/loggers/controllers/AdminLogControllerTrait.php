<?php
/**
 * @copyright Reinvently (c) 2018
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\components\loggers\controllers;

use reinvently\ondemand\core\components\loggers\models\AdminLog;
use reinvently\ondemand\core\controllers\admin\AdminController;
use Yii;

/**
 * Trait AdminLogControllerTrait
 * @package reinvently\ondemand\core\components\loggers\controllers
 *
 * @mixin AdminController
 */
trait AdminLogControllerTrait
{
    /** @var  AdminLog */
    private $adminLog;

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
        $this->adminLog = new AdminLog();
        $user = $this->getUser();
        if ($user) {
            $this->adminLog->userId = $user->id;
        }

        $this->adminLog->startedAt = time();
        $this->adminLog->ip = ip2long(Yii::$app->request->userIP);
        $this->adminLog->route = $this->route;
        $this->adminLog->generateHtmlRequestMethod(Yii::$app->request);
        $this->adminLog->generateHtmlRequestHeaders(Yii::$app->request->getHeaders()->toArray());
        $bodyParams = Yii::$app->request->getBodyParams();
        $queryParams = Yii::$app->request->getQueryParams();
        $this->adminLog->generateHtmlRequestParams(array_merge(
            !empty($bodyParams) ? $bodyParams : [],
            !empty($queryParams) ? $queryParams : []
        ));

        return $this->adminLog->save();
    }

    /**
     * @return bool $result
     */
    public function saveLogResponse()
    {
        if (!$this->adminLog) {
            return false;
        }

        $user = $this->getUser();
        if($user) {
            $this->adminLog->userId = $user->id;
        }
        $this->adminLog->finishedAt = time();

        return $this->adminLog->save();
    }

}
