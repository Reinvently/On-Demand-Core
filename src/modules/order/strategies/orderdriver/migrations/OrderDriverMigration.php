<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\order\strategies\orderdriver\migrations;

use Yii;
use yii\db\Migration;

/**
 * Class OrderDriverMigration
 * @package reinvently\ondemand\core\modules\order\strategies\orderdriver\migrations
 */
class OrderDriverMigration extends Migration
{
    public function up()
    {
        $this->addColumn('order', 'driverId', $this->integer() . ' UNSIGNED');
    }

    public function down()
    {
        $this->dropColumn('order', 'driverId');
    }
}