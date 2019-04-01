<?php
/**
 * @copyright Reinvently (c) 2019
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\pushnotification\models;
use Codeception\Util\HttpCode;
use paragraph1\phpFCM\Message;
use reinvently\ondemand\core\components\helpers\CoreHelper;
use reinvently\ondemand\core\components\loggers\models\ExceptionLog;
use reinvently\ondemand\core\components\model\CoreModel;
use reinvently\ondemand\core\modules\pushnotification\commands\PushNotificationController;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

/**
     * This is the model class for table "async_email".
     *
     * @property string $id
     * @property string $object
     * @property integer $retries
     * @property integer $status
     * @property string $errorText
     * @property integer $version
     * @property integer $createAt
     * @property string $trace
     * @property string $json
     * @property int $toUserId
     * @property int $toClientId
     * @property string $from
     */
class AsyncPushNotification extends CoreModel
{
    const STATUS_NEW = 1;
    const STATUS_IN_PROGRESS = 2;
    const STATUS_SENT_SUCCESS = 3;
    const STATUS_FAILED = 4;

    /**
     * Sends the given message.
     * @param Message $message message instance to be sent
     * @param int $userId
     * @param int $clientId
     * @return boolean whether the message has been sent successfully
     */
    public function send($message, $userId, $clientId)
    {
        try {
            return static::createByMessage($message, $userId, $clientId);
        } catch(\Exception $e) {
            ExceptionLog::saveException($e);
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'push_notification';
    }

    /**
     * @inheritDoc
     */
    public function optimisticLock()
    {
        return 'version';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['object'], 'string', 'max' => 10000000],
            [['retries', 'status', 'version', 'createAt'], 'integer'],
            [['errorText', 'json', 'toUserId', 'toClientId'], 'default', 'value' => ''],
            ['retries', 'default', 'value' => 0],
            ['version', 'default', 'value' => 0],
            ['status', 'default', 'value' => self::STATUS_NEW],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'object' => 'Object',
            'retries' => 'Retries',
            'status' => 'Status',
            'errorText' => 'Error Text',
        ];
    }

    /**
     * @param string $encodedString
     * @return Message
     */
    public static function unpack($encodedString)
    {
        return unserialize($encodedString);
    }

    /**
     * @param Message $message
     * @return string
     */
    public static function pack($message)
    {
        return serialize($message);
    }

    /**
     * @param Message $message
     * @param int $userId only for logs
     * @param int $clientId only for logs
     * @return bool
     */
    public static function createByMessage(Message $message, $userId = null, $clientId = null)
    {

        try {
            $asyncPushNotification = new AsyncPushNotification();
            $asyncPushNotification->object = AsyncPushNotification::pack($message);
            $asyncPushNotification->json = json_encode($message->jsonSerialize());
            $asyncPushNotification->toUserId = $userId;
            $asyncPushNotification->toClientId = $clientId;
            $asyncPushNotification->createAt = time();
            $asyncPushNotification->trace = (new \Exception)->getTraceAsString();

            if (!$asyncPushNotification->save()) {
                throw new \LogicException('AsyncPushNotification was not saved: '
                    . var_export($asyncPushNotification->getFirstErrors(), true));
            }

            return true;
        } catch(\Exception $e) {
            ExceptionLog::saveException($e);
        }
        return false;

    }

    /**
     *
     */
    public static function sendAllByScheduler()
    {
        /** @var AsyncPushNotification[] $asyncPushNotifications */
        $asyncPushNotifications = AsyncPushNotification::find()
            ->select('id')
            ->where(['status' => [static::STATUS_NEW, static::STATUS_FAILED]])
            ->andWhere('retries < 5')
            ->all();

        if (!$asyncPushNotifications) {
            return;
        }

        foreach ($asyncPushNotifications as $asyncEmail) {
            $asyncEmail->createProcess();
        }
    }

    /**
     *
     */
    protected function createProcess()
    {
        $pipes = [];
        $descriptorSpec = [['pipe', 'r']];

        $process = proc_open(PushNotificationController::SEND_COMMAND . ' ' . $this->id, $descriptorSpec,
            $pipes,
            \Yii::$app->basePath
        );

        if (is_resource($process)) {
            fclose($pipes[0]);
        }
    }

    /**
     *
     */
    public function sendByScheduler()
    {

        try {
            if (!$this->saveStatusInProgress()) {
                return;
            }

            $this->status = self::STATUS_SENT_SUCCESS;

            $message = self::unpack($this->object);
            $response = \Yii::$app->fcm->send($message);
            $responseBodyJson = $response->getBody()->getContents();
            $responseBody = CoreHelper::jsonSaveDecode($responseBodyJson);

//            throw new Exception('id:' . $this->id . ' response:' . ArrayHelper::getValue($responseBody, 'success'));
            if (
                $response->getStatusCode() !== HttpCode::OK
                || !$responseBody
                || ArrayHelper::getValue($responseBody, 'success') === null
            ) {
                throw new Exception('id:' . $this->id . ' response:' . $responseBodyJson);
            }

            if (
                ArrayHelper::getValue($responseBody, 'success') == 0
                || ArrayHelper::getValue($responseBody, 'failure') > 0
            ){
                $clientPushToken = ClientPushToken::findOne(['clientId' => $this->toClientId]);
                if (!$clientPushToken) {
                    $this->errorText = 'Not found clientId:' . $this->toClientId;
                    $this->status = self::STATUS_FAILED;
                } else {
                    $array = CoreHelper::jsonSaveDecode($this->json);
                    if (!$array) {
                        throw new Exception('Bad json id:' . $this->id . ' json:' . $this->json);
                    }
                    $token = ArrayHelper::getValue($array, 'to');
                    if (!$token) {
                        throw new Exception('Empty token id:' . $this->id . ' json:' . $this->json);
                    }
                    if ($clientPushToken->token == $token) {
                        $clientPushToken->delete();
                        $this->errorText = 'Token deleted. response:' . $responseBodyJson;
                        $this->status = self::STATUS_FAILED;
                    } else {
                        $this->errorText = 'Old Token. response:' . $responseBodyJson;
                        $this->status = self::STATUS_FAILED;
                    }
                }
            }

//{
//    "multicast_id": 4971851742456621220,
//  "success": 1,
//  "failure": 0,
//  "canonical_ids": 0,
//  "results": [
//    {
//        "message_id": "0:1551180901959220%2ca2cde42ca2cde4"
//    }
//  ]
//}

//{
//    "multicast_id": 6246344784042747172,
//  "success": 0,
//  "failure": 1,
//  "canonical_ids": 0,
//  "results": [
//    {
//        "error": "NotRegistered"
//    }
//  ]
//}

//            $this->object = self::pack($message);
        } catch (\Exception $e) {
            ExceptionLog::saveException($e);
            $this->errorText = $e->getMessage();
            $this->status = self::STATUS_FAILED;
        }

        try {
            $this->retries++;
            $this->save();
        } catch (\Exception $e) {
            ExceptionLog::saveException($e);
        }

    }

    /**
     * @return bool
     */
    protected function saveStatusInProgress()
    {
        if (!in_array($this->status, [AsyncPushNotification::STATUS_NEW, AsyncPushNotification::STATUS_FAILED])) {
            return false;
        }
        $this->status = static::STATUS_IN_PROGRESS;
        return $this->save(true, ['status']);
    }

    public function afterValidate()
    {
        $this->errorText = mb_substr($this->errorText, 0, 255, 'ASCII');
        $this->json = mb_substr($this->json, 0, 0xfffe, 'ASCII');
        return parent::afterValidate();
    }
}
