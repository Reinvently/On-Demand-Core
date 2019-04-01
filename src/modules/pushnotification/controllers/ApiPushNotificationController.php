<?php
/**
 * @copyright Reinvently (c) 2019
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\pushnotification\controllers;


use Codeception\Util\HttpCode;
use linslin\yii2\curl\Curl;
use reinvently\ondemand\core\components\helpers\CoreHelper;
use reinvently\ondemand\core\components\loggers\models\ExceptionLog;
use reinvently\ondemand\core\controllers\rest\ApiTameController;
use reinvently\ondemand\core\exceptions\LogicException;
use reinvently\ondemand\core\modules\pushnotification\models\ClientPushToken;
use reinvently\ondemand\core\modules\user\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class ApiPushNotificationController extends ApiTameController
{
    public function behaviors()
    {
        $verbs = [
            'verbs' => [
                'class' => \yii\filters\VerbFilter::class,
                'actions' => [
                    'setToken' => ['post'],
                ]
            ],
        ];
        return ArrayHelper::merge($verbs, parent::behaviors());
    }

    public function actionSetToken()
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user || !$user->currentClient) {
            throw new ForbiddenHttpException();
        }

        $token = \Yii::$app->request->getBodyParam('token');
        if (!$token) {
            throw new BadRequestHttpException(\Yii::t('yii', 'Missing required parameters: {params}', [
                'params' => 'token',
            ]));
        }

        $curl = new Curl();
        $curl->setHeaders(['Authorization' => 'key=' . \Yii::$app->fcm->apiKey]);
        $response = CoreHelper::jsonSaveDecode($curl->get('https://iid.googleapis.com/iid/info/' . $token));

        if ($curl->responseCode !== HttpCode::OK) {
            ExceptionLog::saveException(new LogicException(json_encode([
                'token' => $token,
                'response' => $response,
                'responseCode' => $curl->responseCode,
            ])));
            $message = ArrayHelper::getValue($response, 'error');
            throw new BadRequestHttpException($message);
        }

        $platform = ArrayHelper::getValue($response, 'platform');
        $application = ArrayHelper::getValue($response, 'application');
        $applicationVersion = ArrayHelper::getValue($response, 'applicationVersion');
        $authorizedEntity = ArrayHelper::getValue($response, 'authorizedEntity');

        if (!$platform || !$application || !$applicationVersion || !$authorizedEntity) {
            ExceptionLog::saveException(new LogicException(json_encode([
                'token' => $token,
                'response' => $response,
                'responseCode' => $curl->responseCode,
            ])));
        }

        $clientPushToken = ClientPushToken::findOne(['clientId' => $user->currentClient->id]);
        if (!$clientPushToken) {
            $clientPushToken = new ClientPushToken();
            $clientPushToken->clientId = $user->currentClient->id;
            $clientPushToken->userId = $user->id;
        }
        $clientPushToken->token = $token;
        $clientPushToken->platform = $platform;
        $clientPushToken->application = $application;
        $clientPushToken->applicationVersion = $applicationVersion;
        $clientPushToken->authorizedEntity = $authorizedEntity;
        if (!$clientPushToken->save()) {
            ExceptionLog::saveException(new LogicException(Json::errorSummary($clientPushToken)));
        }

        return $this->getTransport()->responseSuccess();
    }

    public function actionClearToken()
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user || !$user->currentClient) {
            throw new ForbiddenHttpException();
        }

        $clientPushToken = ClientPushToken::findOne(['clientId' => $user->currentClient->id]);
        if (!$clientPushToken) {
            throw new NotFoundHttpException('ClientPushToken not found');
        }
        if ($clientPushToken->delete() === false) {
            ExceptionLog::saveException(new LogicException(Json::errorSummary($clientPushToken)));
        }


        return $this->getTransport()->responseSuccess();
    }

}