<?php
/**
 * @copyright Reinvently (c) 2018
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

/**
 * Created by PhpStorm.
 * User: sglushko
 * Date: 19.03.2018
 * Time: 16:41
 */
namespace reinvently\ondemand\core\modules\invoice\models;

use reinvently\ondemand\core\components\model\CoreModel;
use reinvently\ondemand\core\components\transport\ApiInterface;
use reinvently\ondemand\core\components\transport\ApiTransportTrait;
use reinvently\ondemand\core\exceptions\LogicException;
use reinvently\ondemand\core\components\loggers\models\ExceptionLog;
use reinvently\ondemand\core\modules\invoice\controllers\PaymentController;
use reinvently\ondemand\core\modules\order\models\Order;
use reinvently\ondemand\core\modules\orderproduct\models\OrderProduct;
use reinvently\ondemand\core\modules\setting\models\Setting;
use reinvently\ondemand\core\modules\user\models\User;
use reinvently\ondemand\core\vendor\tasker\daemon\Tasker;
use reinvently\ondemand\core\vendor\tasker\models\TaskerTask;
use yii\db\ActiveQuery;
use yii\db\StaleObjectException;

/**
 * This is the model class for table "invoice".
 *
 * @property integer id
 * @property integer userId
 * @property integer orderId
 * @property integer subTotal without field in db
 * @property integer total without field in db
 * @property integer status
 * @property integer chargeDate
 * @property integer taskerTaskId
 * @property integer createAt
 * @property integer updateAt
 * @property integer type
 * @property integer monthNumber
 * @property integer v
 * @property string  debug
 * @property integer charged
 *
 * @property User user
 * @property Order order
 * @property TaskerTask taskerTask
 */
class Invoice extends CoreModel implements ApiInterface
{
    use ApiTransportTrait;

    const STATUS_DUE = 1;
    const STATUS_OVER_DUE = 2;
    const STATUS_PAID = 3;
    const STATUS_ANNULLED_WITHOUT_PAID = 4;
    const STATUS_PROCESSING = 5;

    const TYPE_ONE_TIME_INSTALLATION_CHARGE = 1;
    const TYPE_MONTHLY = 2;
    const TYPE_LEASE_BALANCE_AT_ONCE = 3;

    /** @var User */
    public $userModelClass = User::class;

    /** @var Order */
    public $orderModelClass = Order::class;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'invoice';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'userId', 'orderId', 'status', 'createAt', 'updateAt', 'v',
                    'chargeDate', 'taskerTaskId', 'type', 'monthNumber',
                ],
                'integer'
            ],
            [['orderId'], 'validateSameInvoiceCreated'],
            [['debug'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'subTotal' => 'Sub Total',
            'total' => 'Total',
            'charged' => 'Charged',
            'createAt' => 'Create At',
            'updateAt' => 'Update At',
            'v' => 'V',
            'debug' => 'Debug',
        ];
    }

    /**
     * @inheritDoc
     */
    public function optimisticLock()
    {
        return 'v';
    }

    /**
     * @inheritDoc
     */
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->createAt = time();

            //

            $task = new TaskerTask();
            $task->timeNextRun = $this->chargeDate;
            $task->status = Tasker::TASK_STATUS_WAITING;

            if (!$task->save()) {
                throw new LogicException();
            }

            $this->taskerTaskId = $task->id;

        }

        $this->updateAt = time();

        return parent::beforeSave($insert);
    }

    /**
     * @inheritDoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            $task = TaskerTask::findOne($this->taskerTaskId);

            $task->cmd = PaymentController::CMD_PREPARE_TO_CHARGE . ' ' . $this->id;

            if (!$task->save()) {
                throw new LogicException();
            }

        }

        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @return int
     */
    public function getSubTotal()
    {
        /** @var InvoiceItem[] $invoiceItems */
        $invoiceItems = InvoiceItem::find()->where(['invoiceId' => $this->id])->all();
        $subTotal = 0;
        if ($invoiceItems) {
            foreach ($invoiceItems as $invoiceItem) {
                $subTotal += $invoiceItem->getTotal();
            }
        }
        if ($subTotal < 0) {
            $subTotal = 0;
        }
        return $subTotal;
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        $total = $this->getSubTotal();

        /** @var InvoiceItemDiscount[] $invoiceItems */
        $invoiceItems = InvoiceItemDiscount::find()->where(['invoiceId' => $this->id])->all();
        if ($invoiceItems) {
            foreach ($invoiceItems as $invoiceItem) {
                $total += $invoiceItem->getTotal();
            }
        }
        $invoiceItems = InvoiceItemTax::find()->where(['invoiceId' => $this->id])->all();
        if ($invoiceItems) {
            foreach ($invoiceItems as $invoiceItem) {
                $total += $invoiceItem->getTotal();
            }
        }
        if ($total < 0) {
            $total = 0;
        }
        return $total;
    }

    /**
     * array
     */
    public function getInvoiceItemsArray()
    {
        $invoiceItemsArray = [];

        $invoiceItemsArray['items'] = [];
        $invoiceItemsArray['subTotal'] = [];
        $invoiceItemsArray['total'] = [];

        /** @var InvoiceItem[] $invoiceItems */
        $invoiceItems = InvoiceItem::find()->where(['invoiceId' => $this->id])->all();

        if ($invoiceItems) {
            foreach ($invoiceItems as $invoiceItem) {
                $invoiceItemsArray['items'][] = [
                    'title' => $invoiceItem->title,
                    'count' => $invoiceItem->count,
                    'price' => $invoiceItem->price,
                ];
            }
        }

        $invoiceItemsArray['subTotal'][] = [
            'title' => 'Sub Total',
            'price' => $this->getSubTotal(),
        ];

        /** @var InvoiceItemDiscount[] $invoiceItems */
        $invoiceItems = InvoiceItemDiscount::find()->where(['invoiceId' => $this->id])->all();
        if ($invoiceItems) {
            foreach ($invoiceItems as $invoiceItem) {
                $invoiceItemsArray['subTotal'][] = [
                    'title' => $invoiceItem->title,
                    'count' => $invoiceItem->count,
                    'price' => $invoiceItem->price,
                ];
            }
        }

        /** @var InvoiceItemTax[] $invoiceItems */
        $invoiceItems = InvoiceItemTax::find()->where(['invoiceId' => $this->id])->all();
        if ($invoiceItems) {
            foreach ($invoiceItems as $invoiceItem) {
                $invoiceItemsArray['subTotal'][] = [
                    'title' => $invoiceItem->title,
                    'count' => $invoiceItem->count,
                    'price' => $invoiceItem->price,
                ];
            }
        }

        $invoiceItemsArray['total'][] = [
            'title' => 'Total',
            'price' => $this->getTotal(),
        ];

        return $invoiceItemsArray;
    }

    /**
     * @return integer
     */
    public function getCharged()
    {
        $transaction = Transaction::findOne(['invoiceId' => $this->id]);

        if (!$transaction) {
            return 0;
        }

        return $transaction->amount;
    }

    /**
     * @throws LogicException
     */
    public function annul()
    {
        if ($this->status != static::STATUS_DUE) {
            throw new LogicException('bed status');
        }
        $this->status = static::STATUS_ANNULLED_WITHOUT_PAID;
        try {
            if (!$this->save()) {
                throw new LogicException(var_export($this->getErrors(), true));
            }
        } catch (StaleObjectException $e) {
            ExceptionLog::saveException($e);
        }
        $task = TaskerTask::findOne($this->taskerTaskId);
        if ($task && $task->status == Tasker::TASK_STATUS_READY_TO_RUN) {
            $task->status = Tasker::TASK_STATUS_CANCELED;
            $task->save();
        }
    }

    /**
     * @param string $attribute
     */
    public function validateSameInvoiceCreated($attribute)
    {
        if ($this->isSameInvoiceCreated()) {
            $this->addError($attribute,'Same invoice already exists');
        }
    }

    /**
     * @return bool
     */
    public function isSameInvoiceCreated() {
        /** @var ActiveQuery $query */
        $query = static::find()
            ->where([
                'type' => $this->type,
                'monthNumber' => $this->monthNumber,
                'orderId' => $this->orderId,
            ])
            ->andWhere(['<>', 'status', static::STATUS_ANNULLED_WITHOUT_PAID]);

        if (!$this->isNewRecord) {
            $query->andWhere(['<>', 'id', $this->id]);
        }

        return $query->exists();
    }

    /**
     * @throws LogicException
     */
    protected function createInvoiceItemBaseInstallation()
    {
        if ($this->type != static::TYPE_ONE_TIME_INSTALLATION_CHARGE) {
            return;
        }

        $settingInstallationFee = Setting::findOne(Setting::INSTALLATION_FEE);

        if (!$settingInstallationFee) {
            throw new LogicException();
        }

        $invoiceItem = new InvoiceItem();
        $invoiceItem->price = $settingInstallationFee->value;
        $invoiceItem->count = 1;
        $invoiceItem->title = 'Base installation';
        $invoiceItem->description = '';
        $invoiceItem->invoiceId = $this->id;
        if (!$invoiceItem->save()) {
            throw new LogicException(var_export($invoiceItem->getErrors(), true));
        }
    }

    /**
     * @param int $totalWithoutTax
     * @throws LogicException
     */
    protected function createInvoiceItemTax($totalWithoutTax)
    {
        $settingTax = Setting::findOne(Setting::INVOICE_TAX);

        if (!$settingTax) {
            throw new LogicException();
        }

        $invoiceItem = new InvoiceItemTax();
        $invoiceItem->price = round($totalWithoutTax * $settingTax->value / 100, 0, PHP_ROUND_HALF_EVEN);
        $invoiceItem->count = 1;
        $invoiceItem->title = 'Tax';
        $invoiceItem->description = '';
        $invoiceItem->invoiceId = $this->id;
        if (!$invoiceItem->save()) {
            throw new LogicException(var_export($invoiceItem->getErrors(), true));
        }

    }

    /**
     * @param int $totalWithoutDiscount
     * @throws LogicException
     */
    protected function createInvoiceItemDiscount($totalWithoutDiscount)
    {
        $setting = Setting::findOne(Setting::ONE_TIME_PLAN_DISCOUNT);

        if (!$setting) {
            throw new LogicException();
        }

        $invoiceItem = new InvoiceItemDiscount();
        $invoiceItem->price = round(-1 * $totalWithoutDiscount * $setting->value / 100, 0, PHP_ROUND_HALF_EVEN);
        $invoiceItem->count = 1;
        $invoiceItem->title = 'discount ' . $setting->value . '%';
        $invoiceItem->description = '';
        $invoiceItem->invoiceId = $this->id;
        if (!$invoiceItem->save()) {
            throw new LogicException(var_export($invoiceItem->getErrors(), true));
        }

    }


    /**
     * @param OrderProduct[] $orderProducts
     * @throws LogicException
     */
    public function createInvoiceItems($orderProducts)
    {

        $this->createInvoiceItemBaseInstallation();

        foreach ($orderProducts as $orderProduct) {
            $invoiceItem = new InvoiceItem();
            $invoiceItem->price = $orderProduct->price;
            $invoiceItem->count = $orderProduct->count;
            $invoiceItem->title = $orderProduct->title;
            $invoiceItem->description = '';
            $invoiceItem->invoiceId = $this->id;
            if (!$invoiceItem->save()) {
                throw new LogicException(var_export($invoiceItem->getErrors(), true));
            }
        }

        if ($this->type == static::TYPE_LEASE_BALANCE_AT_ONCE) {
            $totalWithoutDiscount = $this->getTotal();
            $this->createInvoiceItemDiscount($totalWithoutDiscount);
        }

        $totalWithoutTax = $this->getTotal();
        $this->createInvoiceItemTax($totalWithoutTax);

    }

    /**
     * @return array
     */
    public function getItemForApi()
    {
        return [
            'id' => $this->id,
            'userId' => $this->userId,
            'orderId' => $this->orderId,
            'status' => $this->status,
            'type' => $this->type,
            'monthNumber' => $this->monthNumber,
            'total' => $this->total,
            'charged' => $this->charged,
            'chargedDate' => $this->chargeDate,
            'invoiceItemsArray' => $this->getInvoiceItemsArray(),
        ];
    }

    /**
     * @return array
     */
    public function getItemShortForApi()
    {
        return [
            'id' => $this->id,
            'userId' => $this->userId,
            'orderId' => $this->orderId,
            'status' => $this->status,
            'type' => $this->type,
            'monthNumber' => $this->monthNumber,
            'total' => $this->total,
            'charged' => $this->charged,
            'chargedDate' => $this->chargeDate,
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUser() {
        /** @var User $class */
        $class = $this->userModelClass;

        return $this->hasOne($class::className(), ['id' => 'userId']);
    }

    /**
     * @return ActiveQuery
     */
    public function getOrder() {
        /** @var Order $class */
        $class = $this->orderModelClass;

        return $this->hasOne($class::className(), ['id' => 'orderId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaskerTask()
    {
        return $this->hasOne(TaskerTask::class, ['id' => 'taskerTaskId']);
    }

}
