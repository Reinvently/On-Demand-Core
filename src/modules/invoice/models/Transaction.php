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
 * Time: 16:19
 */
namespace reinvently\ondemand\core\modules\invoice\models;

use reinvently\ondemand\core\components\model\CoreModel;

/**
 * This is the model class for table "transaction".
 *
 * @property int $id
 * @property int $invoiceId
 * @property string $transactionId
 * @property int $paymentMethodId
 * @property int $status
 * @property int $createAt
 * @property int $updateAt
 * @property int $amount
 */
class Transaction extends CoreModel
{
    const STATUS_AUTHORIZED = 1;
    const STATUS_SUBMITTED_FOR_SETTLEMENT = 2;
    const STATUS_VOIDED = 3;
    const STATUS_FAILED = 4;
    const STATUS_PROCESSED = 5;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'transaction';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['invoiceId', 'paymentMethodId', 'status', 'createAt', 'updateAt', 'amount'], 'integer'],
            [['transactionId'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'invoiceId' => 'Invoice ID',
            'transactionId' => 'Transaction ID',
            'status' => 'Status',
            'createAt' => 'Create At',
            'updateAt' => 'Update At',
            'amount' => 'Amount',
        ];
    }

    /**
     * @inheritDoc
     */
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->createAt = time();
        }

        $this->updateAt = time();

        return parent::beforeSave($insert);
    }

}
