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
 * Time: 17:33
 */

namespace reinvently\ondemand\core\modules\invoice\migrations;

use reinvently\ondemand\core\modules\setting\models\Setting;
use yii\db\Migration;

class InvoiceTablesMigration extends Migration
{
    public function up()
    {
        $this->createTable('invoice', [
            'id' => $this->primaryKey(),
            'userId' => $this->integer()->unsigned(),
            'orderId' => $this->integer()->unsigned(),
            'status' => $this->smallInteger()->unsigned(),
            'chargeDate' => $this->integer()->unsigned(),
            'taskerTaskId' => $this->integer()->unsigned(),
            'createAt' => $this->integer()->unsigned(),
            'updateAt' => $this->integer()->unsigned(),
            'type' => $this->smallInteger()->unsigned(),
            'monthNumber' => $this->smallInteger()->unsigned(),
            'v' => $this->integer()->unsigned(),
            'debug' => $this->text(),
        ]);

        $this->createTable('invoice_item', [
            'id' => $this->primaryKey(),
            'invoiceId' => $this->integer()->unsigned(),
            'title' => $this->string(),
            'description' => $this->text(),
            'price' => $this->integer(),
            'count' => $this->integer(),
            'createAt' => $this->integer()->unsigned(),
            'updateAt' => $this->integer()->unsigned(),
            'v' => $this->integer()->unsigned(),
        ]);
        $this->createIndex('invoiceId', 'invoice_item', 'invoiceId');

        $this->createTable('invoice_item_discount', [
            'id' => $this->primaryKey(),
            'invoiceId' => $this->integer()->unsigned(),
            'title' => $this->string(),
            'description' => $this->text(),
            'price' => $this->integer(),
            'count' => $this->integer(),
            'createAt' => $this->integer()->unsigned(),
            'updateAt' => $this->integer()->unsigned(),
            'v' => $this->integer()->unsigned(),
        ]);
        $this->createIndex('invoiceId', 'invoice_item_discount', 'invoiceId');

        $this->createTable('invoice_item_tax', [
            'id' => $this->primaryKey(),
            'invoiceId' => $this->integer()->unsigned(),
            'title' => $this->string(),
            'description' => $this->text(),
            'price' => $this->integer(),
            'count' => $this->integer(),
            'createAt' => $this->integer()->unsigned(),
            'updateAt' => $this->integer()->unsigned(),
            'v' => $this->integer()->unsigned(),
        ]);
        $this->createIndex('invoiceId', 'invoice_item_tax', 'invoiceId');

        $this->batchInsert('setting', ['id', 'name', 'value'], [
            [Setting::INVOICE_TAX, 'Invoice Tax (percents)', '8.25'],
            [Setting::INSTALLATION_FEE, 'Installation Fee (cents)', '19900'],
            [Setting::ONE_TIME_PLAN_DISCOUNT, 'One Time Plan Discount (percents)', '10'],
        ]);

        $this->createTable('payment_method', [
            'id' => $this->primaryKey(),
            'orderId' => $this->integer()->unsigned(),
            'userId' => $this->integer()->unsigned(),
            'token' => $this->string(),
            'percent' => $this->smallInteger(),
        ]);

        $this->createTable('transaction', [
            'id' => $this->primaryKey(),
            'invoiceId' => $this->integer()->unsigned(),
            'paymentMethodId' => $this->integer()->unsigned(),
            'transactionId' => $this->string(),
            'status' => $this->smallInteger()->unsigned(),
            'createAt' => $this->integer()->unsigned(),
            'updateAt' => $this->integer()->unsigned(),
            'amount' => $this->integer()
        ]);

    }

    public function down()
    {
        $this->dropTable('invoice');
        $this->dropTable('invoice_item');
        $this->dropTable('invoice_item_discount');
        $this->dropTable('invoice_item_tax');

        $this->truncateTable('setting');

        $this->dropTable('payment_method');
        $this->dropTable('transaction');

    }

}