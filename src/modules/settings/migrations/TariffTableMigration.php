<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\settings\migrations;

use Yii;
use yii\db\Migration;

class TariffTableMigration extends Migration
{
    public function up()
    {
        $this->createTable('tariff', [
            'id' => $this->integer() . ' UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'name' => $this->string(),
            'price' => $this->integer() . ' UNSIGNED',
            'employeePrice' => $this->integer() . ' UNSIGNED',
            'additionalPrice' => $this->integer() . ' UNSIGNED',
            'description' => $this->text(),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci');
    }

    public function down()
    {
        $this->dropTable('tariff');
        return true;
    }
}
