<?php
/**
 * @copyright Reinvently (c) 2019
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\pushnotification\commands;

use reinvently\ondemand\core\components\loggers\models\ExceptionLog;
use reinvently\ondemand\core\modules\pushnotification\models\AsyncPushNotification;
use yii\console\ExitCode;
use reinvently\ondemand\core\controllers\console\ConsoleController;

class PushNotificationController extends ConsoleController
{
    const SEND_COMMAND = 'php yii push-notification/send-async-push-notification';
    const SEND_ALL_BY_SCHEDULER_COMMAND = 'php yii push-notification/send-all-by-scheduler';

    public function actionSendAsyncPushNotification($id)
    {
        if (empty($id)) {
            ExceptionLog::saveException(new \LogicException('Param id is required'));
            return ExitCode::OK;
        }

        /** @var AsyncPushNotification $asyncPushNotification */
        $asyncPushNotification = AsyncPushNotification::findOne($id);

        if (!$asyncPushNotification) {
            ExceptionLog::saveException(
                new \LogicException('AsyncPushNotification: ' . $id . ' is not found'));
        }
        $asyncPushNotification->sendByScheduler();

        return ExitCode::OK;
    }

    public function actionSendAllByScheduler()
    {
        AsyncPushNotification::sendAllByScheduler();

        return ExitCode::OK;
    }

    //    /**
//     * @param string $app
//     * @param string $deviceToken
//     * @param array $payloadData
//     */
//    public function actionPushAndroid($app, $deviceToken, $payloadData)
//    {
//        $envType = \Yii::$app->params['pushMode'][$app];
//        $params = \Yii::$app->params['gcm'][$app][$envType];
//
//        $gcmApiKey = $params['apiKey'];
//        $sender = new \PHP_GCM\Sender($gcmApiKey);
//        $collapseKey = '';
//
//        $message = new \PHP_GCM\Message($collapseKey, $payloadData);
//
//        $deviceRegistrationId = $deviceToken;
//        $numberOfRetryAttempts = 1;
//        $sender->sendMulti($message, [$deviceRegistrationId], $numberOfRetryAttempts);
//
//    }
//
//    /**
//     * @param string $app
//     * @param string $deviceToken
//     * @param array $payloadData
//     */
//    public function actionPushIos($app, $deviceToken, $payloadData)
//    {
//
//        $envType = \Yii::$app->params['pushMode'][$app];
//        $apnsParams = \Yii::$app->params['apns'][$app][$envType];
//        $certificate = $apnsParams['certificatePath'];
//        $passPhrase = $apnsParams['passPhrase'];
//
//        $apnsEnv = \ApnsPHP_Abstract::ENVIRONMENT_SANDBOX;
//        if ($envType == 'prod') {
//            $apnsEnv = \ApnsPHP_Abstract::ENVIRONMENT_PRODUCTION;
//        }
//
//        $push = new \ApnsPHP_Push($apnsEnv, $certificate);
//        $push->setProviderCertificatePassphrase($passPhrase);
//        $push->connect();
//
//        $msg = new ApnsPHP_Message();
//        $msg->addRecipient($deviceToken);
//        $this->configureApnsMessage($msg, $payloadData);
//        $push->add($msg);
//        $push->send();
//        $push->disconnect();
//    }
//
//    /**
//     * @param ApnsPHP_Message $msg
//     * @param $messageParams
//     */
//    protected function configureApnsMessage(ApnsPHP_Message $msg, $messageParams)
//    {
//        if(isset($messageParams['id'])) {
//            $msg->setCustomIdentifier($messageParams['id']);
//        }
//
//        if(isset($messageParams['expiry'])) {
//            $msg->setExpiry($messageParams['expiry']);
//        }
//
//        if(isset($messageParams['sound'])) {
//            $msg->setSound($messageParams['sound']);
//        }
//
//        if(isset($messageParams['contentAvailable'])) {
//            $msg->setContentAvailable($messageParams['contentAvailable']);
//        }
//
//        if(isset($messageParams['text'])) {
//            $msg->setText($messageParams['text']);
//        }
//
//        if(isset($messageParams['customProperties'])) {
//            foreach($messageParams['customProperties'] as $property => $value) {
//                $msg->setCustomProperty($property, $value);
//            }
//        }
//    }
}