<?php
/**
 * @copyright Reinvently (c) 2018
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

/**
 * Created by PhpStorm.
 * User: sglushko
 * Date: 28.03.2018
 * Time: 12:49
 */


namespace reinvently\ondemand\core\modules\asyncemail\commands;


use reinvently\ondemand\core\components\loggers\models\ExceptionLog;
use reinvently\ondemand\core\modules\asyncemail\models\AsyncEmail;
use yii\console\Controller;
use yii\console\ExitCode;

abstract class EmailController extends Controller
{
    const SEND_COMMAND = 'php yii email/send-async-email';
    const SEND_ALL_BY_SCHEDULER_COMMAND = 'php yii email/send-all-by-scheduler';

    public function actionSendAsyncEmail($id)
    {
        if (empty($id)) {
            ExceptionLog::saveException(new \LogicException('Param id is required'));
            return ExitCode::OK;
        }

        /** @var AsyncEmail $asyncEmail */
        $asyncEmail = AsyncEmail::findOne($id);

        if (!$asyncEmail) {
            ExceptionLog::saveException(
                new \LogicException('AsyncEmail: ' . $id . ' is not found'));
        }
        $asyncEmail->sendByScheduler();

        return ExitCode::OK;
    }

    public function actionSendAllByScheduler()
    {
        AsyncEmail::sendAllByScheduler();

        return ExitCode::OK;
    }

}