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
 * Time: 17:16
 */

namespace reinvently\ondemand\core\modules\order\strategies\orderinvoice\migrations;

use yii\db\Migration;

class OrderInvoiceMigration extends Migration
{

    public function up()
    {
        $this->addColumn('order', 'leaseLength', $this->integer()->unsigned());
        $this->addColumn('order', 'moveInDate', $this->integer()->unsigned());
        $this->addColumn('order', 'moveOutDate', $this->integer()->unsigned());
    }

    public function down()
    {
        $this->dropColumn('order', 'leaseLength');
        $this->dropColumn('order', 'moveInDate');
        $this->dropColumn('order', 'moveOutDate');
    }
}