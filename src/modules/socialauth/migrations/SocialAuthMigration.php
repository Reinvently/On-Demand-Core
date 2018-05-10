<?php
/**
 * @copyright Reinvently (c) 2018
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\socialauth\migrations;

use yii\db\Migration;

class SocialAuthMigration extends Migration
{
    /**
     * @inheritDoc
     */
    public function up()
    {
        $this->createTable('auth', [
            'id' => $this->primaryKey()->unsigned(),
            'userId' => $this->integer()->unsigned()->notNull(),
            'source' => $this->string()->notNull(),
            'sourceId' => $this->string()->notNull(),
        ]);

        $this->createIndex('userId', 'auth', 'userId');

    }

    /**
     * @inheritDoc
     */
    public function down()
    {
        $this->dropTable('auth');
        return true;
    }


}