<?php
/**
 * @copyright Reinvently (c) 2018
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

/**
 * Created by PhpStorm.
 * User: sglushko
 * Date: 21.03.2018
 * Time: 16:17
 */

namespace reinvently\ondemand\core\modules\order\strategies\orderinvoice\traits;

use reinvently\ondemand\core\exceptions\LogicException;
use reinvently\ondemand\core\modules\invoice\models\Invoice;
use reinvently\ondemand\core\modules\orderproduct\models\OrderProduct;
use reinvently\ondemand\core\vendor\tasker\daemon\Tasker;

/**
 * Trait OrderInvoice
 * @package reinvently\ondemand\core\modules\order\strategies\orderinvoice\traits
 *
 * @property int $id
 * @property int $userId
 * @property int $leaseLength months
 * @property int $moveInDate
 * @property int $moveOutDate
 *
 * @property OrderProduct[] orderProducts
 */
trait OrderInvoice
{
    /** @var Invoice */
    public $invoiceModelClass = Invoice::class;


    /**
     * @return array
     */
    static public function getStatusesBeforeCharge()
    {
        return [
        ];
    }

    /**
     * @throws LogicException
     */
    public function runInvoiceTaskerTask()
    {
        $invoices = $this->getInvoicesDue();
        if (!$invoices) {
            return;
        }
        foreach ($invoices as $invoice) {
            if ($invoice->taskerTask->status != Tasker::TASK_STATUS_WAITING) {
                throw new LogicException();
            }
            $invoice->taskerTask->status = Tasker::TASK_STATUS_READY_TO_RUN;
            $invoice->taskerTask->save();
        }
    }

    /**
     * @return Invoice[]
     */
    protected function getInvoicesDue()
    {
        return Invoice::find()->where(['status' => Invoice::STATUS_DUE, 'orderId' => $this->id])->all();
    }

    /**
     *
     */
    protected function annulInvoices() {
        /** @var Invoice[] $invoices */
        $invoices = Invoice::find()->where(['status' => Invoice::STATUS_DUE, 'orderId' => $this->id])->all();
        if (!$invoices) {
            return;
        }
        foreach ($invoices as $invoice) {
            $invoice->annul();
        }
    }

    /**
     *
     */
    public function regenerateInvoices() {
        $this->annulInvoices();
        $orderProducts = $this->orderProducts;

        if (!$orderProducts) {
            throw new LogicException();
        }

        $oneTimeOrderProducts = [];
        $monthlyOrderProducts = [];
        foreach ($orderProducts as $orderProduct) {
            if ($orderProduct->isOneTimePay) {
                $oneTimeOrderProducts[] = $orderProduct;
            } else {
                $monthlyOrderProducts[] = $orderProduct;
            }
        }

        $this->regenerateOneTimeInvoices($oneTimeOrderProducts);
        $this->regenerateMonthlyInvoices($monthlyOrderProducts);

    }

    /**
     * @param OrderProduct[] $orderProducts
     * @throws LogicException
     */
    protected function regenerateOneTimeInvoices($orderProducts) {
        if (!$orderProducts) {
            return;
        }

        $invoice = new Invoice();
        $invoice->userId = $this->userId;
        $invoice->orderId = $this->id;
        $invoice->chargeDate = time();
        $invoice->status = Invoice::STATUS_DUE;
        $invoice->type = Invoice::TYPE_ONE_TIME_INSTALLATION_CHARGE;
        $invoice->taskerTaskId;
        if ($invoice->save()) {
            $invoice->createInvoiceItems($orderProducts);
        }

    }

    protected function getMonthlyChargeDates()
    {
        $monthlyChargeTimes = [];
        for ($i = 1; $i <= $this->leaseLength; $i++) {
            $dateTime = new \DateTime();
            $dateTime->setTimestamp($this->moveInDate);
            $dateTime->add(new \DateInterval('P' . ($i - 1) . 'M'));
            $monthlyChargeTimes[$i] = $dateTime->getTimestamp();
        }
        return $monthlyChargeTimes;
    }

    /**
     * @param $orderProducts
     * @throws LogicException
     */
    protected function regenerateMonthlyInvoices($orderProducts) {
        if (!$orderProducts) {
            return;
        }

        $dates = $this->getMonthlyChargeDates();

        if (!$dates) {
            return;
        }

        foreach ($dates as $monthNumber => $date) {
            $invoice = new Invoice();
            $invoice->userId = $this->userId;
            $invoice->orderId = $this->id;
            $invoice->chargeDate = $date;
            $invoice->status = Invoice::STATUS_DUE;
            $invoice->type = Invoice::TYPE_MONTHLY;
            $invoice->monthNumber = $monthNumber;
            if ($invoice->save()) {
                $invoice->createInvoiceItems($orderProducts);
            }

        }
    }


}