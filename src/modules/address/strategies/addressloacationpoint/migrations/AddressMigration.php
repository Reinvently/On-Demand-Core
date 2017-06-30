<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\modules\address\strategies\addressloacationpoint\migrations;

use Yii;
use yii\db\Migration as YiiMigration;

class AddressMigration extends YiiMigration
{
    public function up()
    {
        $this->addColumn('address', 'locationPointId', $this->string());
    }

    public function down()
    {
        $this->dropColumn('address', 'locationPointId');
    }
}