<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */
/**
 * Created by PhpStorm.
 * User: sglushko
 * Date: 11.10.2017
 * Time: 18:07
 */
namespace reinvently\ondemand\core\modules\user\strategies\pushnotification\migration;

use reinvently\ondemand\core\modules\user\models\Client;
use yii\db\Migration as YiiMigration;

class PushNotificationMigration extends YiiMigration
{
    public function up()
    {
        $this->addColumn(Client::tableName(), 'pushToken', $this->string());
    }

    public function down()
    {
        $this->dropColumn(Client::tableName(), 'pushToken');
    }
}