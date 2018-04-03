<?php
/**
 * @copyright Reinvently (c) 2018
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

/**
 * Created by PhpStorm.
 * User: sglushko
 * Date: 20.03.2018
 * Time: 16:54
 */
namespace reinvently\ondemand\core\modules\invoice\commands;


use reinvently\ondemand\core\exceptions\LogicException;
use reinvently\ondemand\core\components\loggers\models\ExceptionLog;
use reinvently\ondemand\core\modules\invoice\models\Invoice;
use reinvently\ondemand\core\modules\invoice\models\OrderPaymentMethod;
use reinvently\ondemand\core\modules\invoice\models\Transaction;
use reinvently\ondemand\core\vendor\paymentbraintree\Braintree;
use reinvently\ondemand\core\vendor\tasker\daemon\Tasker;
use reinvently\ondemand\core\vendor\tasker\models\TaskerTask;
use yii\console\Controller;
use yii\console\ExitCode;

class PaymentController extends Controller
{
    const CMD_CHARGE = 'php yii payment/charge';
    const CMD_PREPARE_TO_CHARGE = 'php yii payment/prepare-to-charge';

    /** @var Invoice */
    public $invoiceModelClass = Invoice::class;

    /** @var OrderPaymentMethod */
    public $orderPaymentMethodModelClass = OrderPaymentMethod::class;

    public function actionPrepareToCharge($invoiceId)
    {
        /** @var Invoice $invoice */
        $invoice = Invoice::findOne($invoiceId);
        if (!$invoice) {
            throw new LogicException();
        }

        $task = new TaskerTask();
        $task->status = Tasker::TASK_STATUS_READY_TO_RUN;
        $task->cmd = PaymentController::CMD_CHARGE . ' ' . $invoiceId;
        $task->timeNextRun = $invoice->chargeDate + 24 * 3600;

        if (!$task->save()) {
            throw new LogicException();
        }

        $invoice->taskerTaskId = $task->id;
        $invoice->save();

    }

    public function actionCharge($invoiceId)
    {
        $invoice = Invoice::findOne($invoiceId);

        if (!$invoice) {
            throw new LogicException();
        }

        if (!in_array($invoice->status,[Invoice::STATUS_DUE, Invoice::STATUS_OVER_DUE])) {
            throw new LogicException();
        }

        $invoice->status = Invoice::STATUS_PROCESSING;
        if (!$invoice->save()) {
            throw new LogicException();
        }

        if (time() + 24 * 3600 < $invoice->chargeDate) {
            throw new LogicException();
        }

        $paymentMethod = OrderPaymentMethod::findOne(['orderId' => $invoice->orderId]);
        if (!$paymentMethod) {
            throw new LogicException();
        }

        $amount = $invoice->total;

        $braintree = new Braintree();

        try {
            $result = $braintree->sale(
                round($invoice->total / 100, 2, PHP_ROUND_HALF_EVEN),
                $invoice->orderId,
                $paymentMethod->token
            );
        } catch (\Exception $e) {
            ExceptionLog::saveException($e);
            $result = false;
        }

        if ($result) {
            $transaction = new Transaction();
            $transaction->invoiceId = $invoiceId;
            $transaction->status = Transaction::STATUS_SUBMITTED_FOR_SETTLEMENT;
            $transaction->transactionId = $braintree->result->transaction->id;
            $transaction->paymentMethodId = $paymentMethod->id;
            $transaction->amount = $amount;
            if (!$transaction->save()) {
                throw new LogicException();
            }

            $invoice->status = Invoice::STATUS_PAID;
            if (!$invoice->save()) {
                throw new LogicException();
            }

            $this->successPayment($invoice);

        } else {
            $transaction = new Transaction();
            $transaction->invoiceId = $invoiceId;
            $transaction->status = Transaction::STATUS_FAILED;
            if (!empty($braintree->result->transaction->id)) {
                $transaction->transactionId = $braintree->result->transaction->id;
            }
            $transaction->paymentMethodId = $paymentMethod->id;
            $transaction->amount = 0;
            if (!$transaction->save()) {
                throw new LogicException();
            }

            $this->createRechargeTask($invoice);
        }

        return ExitCode::OK;
    }

    /**
     * @param Invoice $invoice
     * @throws LogicException
     */
    protected function successPayment(Invoice $invoice)
    {

    }

    /**
     * @param Invoice $invoice
     * @throws LogicException
     */
    protected function createRechargeTask(Invoice $invoice)
    {
        $task = new TaskerTask();
        $task->timeNextRun = time() + 24 * 3600;
        $task->status = Tasker::TASK_STATUS_READY_TO_RUN;
        $task->cmd = static::CMD_CHARGE . ' ' . $invoice->id;

        if (!$task->save()) {
            throw new LogicException();
        }

        $invoice->status = Invoice::STATUS_OVER_DUE;
        $invoice->taskerTaskId = $task->id;
        $invoice->save();
    }

}