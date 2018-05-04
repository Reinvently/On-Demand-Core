<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\user\migrations;

use reinvently\ondemand\core\modules\role\models\Role;
use Yii;
use yii\db\Migration;

class UserTableMigration extends Migration
{
    public function up()
    {
        $this->createTable('user', [
            // todo https://github.com/yiisoft/yii2/issues/9929
            'id' => $this->integer() . ' UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'email' => $this->string()->unique(),
            'password' => $this->string(),
            'roleId' => $this->integer()->notNull(),
            'firstName' => $this->string(),
            'lastName' => $this->string(),
            'phone' => $this->string()->unique(),
            'createdAt' => $this->integer() . ' UNSIGNED NOT NULL',
            'updatedAt' => $this->integer() . ' UNSIGNED NOT NULL',
            'language' => $this->string(),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci');

        $this->insert('user', [
            'email' => 'admin@provectus.com',
            'password' => Yii::$app->getSecurity()->generatePasswordHash('provectus123'),
            'roleId' => Role::ADMIN,
            'createdAt' => time(),
            'updatedAt' => time()
        ]);
        $this->insert('user', [
            'email' => 'grocer@gmail.com',
            'password' => Yii::$app->getSecurity()->generatePasswordHash('grocer12'),
            'roleId' => Role::USER,
            'createdAt' => time(),
            'updatedAt' => time()
        ]);
    }

    public function down()
    {
        $this->dropTable('user');
        return true;
    }
}
