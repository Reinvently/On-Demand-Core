<?php
/**
 * @copyright Reinvently (c) 2018
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\address\migrations;

use Yii;
use yii\db\Migration;

class AddressTableMigration extends Migration
{
    public function up()
    {
        $this->createTable('address', [
            'id' => $this->integer() . ' UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'userId' => $this->integer() . ' UNSIGNED NOT NULL',
            'latitude' => $this->string(),
            'longitude' => $this->string(),
            'address' => $this->string(),
            'createdAt' => $this->integer() . ' UNSIGNED NOT NULL',
            'updatedAt' => $this->integer() . ' UNSIGNED NOT NULL',
            'country' => $this->string()->notNull(),
            'zip' => $this->string()->notNull(),
            'stateCode' => $this->string(),
            'city' => $this->string(),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci');

        $this->createIndex('country_zip', 'address', ['country', 'zip'], true);
        $this->createIndex('zip', 'address', 'zip', true);

    }

    public function down()
    {
        $this->dropTable('address');
        return true;
    }
}
