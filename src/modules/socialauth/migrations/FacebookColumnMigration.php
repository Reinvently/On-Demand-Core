<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\socialauth\migrations;

use Yii;
use yii\db\Schema;
use yii\db\Migration;

class FacebookColumnMigration extends Migration
{
    public function up()
    {
        $this->addColumn('user', 'facebookId', 'varchar(255) NULL after phone');
    }

}
