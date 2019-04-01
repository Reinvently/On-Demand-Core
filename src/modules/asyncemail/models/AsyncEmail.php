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
 * Time: 18:18
 */


namespace reinvently\ondemand\core\modules\asyncemail\models;


use reinvently\ondemand\core\components\model\CoreModel;
use reinvently\ondemand\core\components\loggers\models\ExceptionLog;
use reinvently\ondemand\core\modules\asyncemail\commands\EmailController;
use reinvently\ondemand\core\modules\asyncemail\helpers\Mailer;
use yii\helpers\Json;
use yii\mail\MessageInterface;


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
 * @property string $title
 * @property string $to
 * @property string $from
 */
class AsyncEmail extends CoreModel
{
    const STATUS_NEW = 1;
    const STATUS_IN_PROGRESS = 2;
    const STATUS_SENT_SUCCESS = 3;
    const STATUS_FAILED = 4;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'async_email';
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
            [['errorText', 'title', 'to', 'from'], 'default', 'value' => ''],
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
     * @return MessageInterface
     */
    public static function unpack($encodedString)
    {
        return unserialize($encodedString);
    }

    /**
     * @param MessageInterface $message
     * @return string
     */
    public static function pack($message)
    {
        return serialize($message);
    }

    /**
     * @param MessageInterface $message
     * @return bool
     */
    public static function createByMessage(MessageInterface $message)
    {
        if (empty($message->getTo())) {
            return false;
        }

        try {
            $asyncEmail = new AsyncEmail();
            $asyncEmail->object = AsyncEmail::pack($message);
            $asyncEmail->title = $message->getSubject();
            $asyncEmail->to = Json::encode($message->getTo());
            $asyncEmail->from = Json::encode($message->getFrom());
            $asyncEmail->createAt = time();
            $asyncEmail->trace = (new \Exception)->getTraceAsString();

            if (!$asyncEmail->save()) {
                throw new \LogicException('AsyncEmail was not saved: '
                    . var_export($asyncEmail->getFirstErrors(), true));
            }
//            $asyncEmail->createProcess(); todo sending only through cycling tasker task

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
        /** @var AsyncEmail[] $asyncEmails */
        $asyncEmails = AsyncEmail::find()
            ->select('id')
            ->where(['status' => [static::STATUS_NEW, static::STATUS_FAILED]])
            ->andWhere('retries < 5')
            ->all();

        if (!$asyncEmails) {
            return;
        }

        foreach ($asyncEmails as $asyncEmail) {
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

        $process = proc_open(EmailController::SEND_COMMAND . ' ' . $this->id, $descriptorSpec, $pipes, \Yii::$app->basePath);

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
            (new Mailer())->sendAfterAsync($message);

            $this->object = self::pack($message);

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
        if (!in_array($this->status, [AsyncEmail::STATUS_NEW, AsyncEmail::STATUS_FAILED])) {
            return false;
        }
        $this->status = static::STATUS_IN_PROGRESS;
        return $this->save(true, ['status']);
    }

    public function afterValidate()
    {
        $this->errorText = mb_substr($this->errorText, 0, 255, 'ASCII');
        return parent::afterValidate();
    }
}
