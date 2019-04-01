<?php
/**
 * @copyright Reinvently (c) 2018
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\components\loggers\controllers;

use reinvently\ondemand\core\components\loggers\models\ApiLog;
use reinvently\ondemand\core\controllers\rest\ApiController;
use Yii;

/**
 * Trait ApiLogControllerTrait
 * @package reinvently\ondemand\core\components\loggers\controllers
 *
 * @mixin ApiController
 */
trait ApiLogControllerTrait
{
    /** @var  ApiLog */
    private $apiLog;

    /**
     * @return bool $result
     */
    public function saveLogRequest()
    {
        $this->apiLog = new ApiLog();
        $user = $this->getUser();
        if ($user && $user->currentClient && $user->currentClient->token) {
            $this->apiLog->token = $user->currentClient->token;
            $this->apiLog->userId = $user->id;
        }

        $this->apiLog->startedAt = time();
        $this->apiLog->ip = ip2long(Yii::$app->request->userIP);
        $this->apiLog->route = $this->route;
        $this->apiLog->generateHtmlRequestMethod(Yii::$app->request);
        $this->apiLog->generateHtmlRequestHeaders(Yii::$app->request->getHeaders()->toArray());
        $bodyParams = Yii::$app->request->getBodyParams();
        $queryParams = Yii::$app->request->getQueryParams();
        $this->apiLog->generateHtmlRequestParams(array_merge(
            !empty($bodyParams) ? $bodyParams : [],
            !empty($queryParams) ? $queryParams : []
        ));

        return $this->apiLog->save();
    }

    /**
     * @return bool $result
     */
    public function saveLogResponse()
    {

        if (!$this->apiLog) {
            return false;
        }

        $user = $this->getUser();
        if($user) {
            $this->apiLog->userId = $user->id;
        }
        $this->apiLog->finishedAt = time();

        if (\Yii::$app->has('response')) {
            $this->apiLog->generateHtmlResponseStatusCode(\Yii::$app->getResponse());
            $this->apiLog->generateHtmlResponseHeaders(\Yii::$app->getResponse()->getHeaders()->toArray());
            $this->apiLog->generateHtmlResponseParams(\Yii::$app->getResponse()->data);
        }

        return $this->apiLog->save();
    }

}