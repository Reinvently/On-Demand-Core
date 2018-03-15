<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\appointment\migrations;

/**
 * Created by PhpStorm.
 * User: sglushko
 * Date: 13.03.2018
 * Time: 12:12
 */
class AppointmentTableMigration extends \yii\db\Migration
{
    public function up()
    {
        $this->createTable('appointment', [
            'id' => $this->primaryKey()->unsigned(),
            'startAt' => $this->integer()->unsigned(),
            'finishAt' => $this->integer()->unsigned(),
            'createdAt' => $this->integer()->unsigned(),
            'updatedAt' => $this->integer()->unsigned(),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci');

        $this->createIndex('startAt', 'appointment', 'startAt');
        $this->createIndex('finishAt', 'appointment', 'finishAt');
    }

    public function down()
    {
        $this->dropTable('appointment');
        return true;
    }
}