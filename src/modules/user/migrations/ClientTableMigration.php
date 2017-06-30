<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\user\migrations;

use Yii;
use yii\db\Migration;


class ClientTableMigration extends Migration
{
    public function up()
    {
        $this->createTable('client', [
            'id' => $this->integer() . ' UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'userId' => $this->integer() . ' UNSIGNED NOT NULL',
            'uuid' => $this->string()->notNull(),
            'token' => $this->string()->notNull()->unique(),
            'type' => $this->string(),
            'ip' => $this->string(),
            'expiredAt' => $this->integer() . ' UNSIGNED NOT NULL'
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci');
    }

    public function down()
    {
        $this->dropTable('client');
        return true;
    }
}