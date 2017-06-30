<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\settings\migrations;

use Yii;
use yii\db\Migration;

/**
 * Class ExtraFeeTableMigration
 * @package reinvently\ondemand\core\modules\settings\migrations
 */
class ExtraFeeTableMigration extends Migration
{

    /**
     *
     */
    public function up()
    {
        $this->createTable('extra_fee', [
            'id' => $this->primaryKey(),
            'tariffId' => $this->integer()->notNull()->unsigned(),
            'title' => $this->string(255),
            'description' => $this->text(),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci');

        $this->createTable('extra_fee_time', [
            'id' => $this->primaryKey(),
            'extraFeeId' => $this->integer(10)->notNull(),
            'timeStart' => $this->time()->notNull(),
            'timeFinish' => $this->time()->notNull(),
        ]);

        $this->createTable('extra_fee_day', [
            'id' => $this->primaryKey(),
            'extraFeeId' => $this->integer(10)->notNull(),
            'day' => $this->integer(2)->notNull(),
        ]);

        $this->createTable('extra_fee_month', [
            'id' => $this->primaryKey(),
            'extraFeeId' => $this->integer(10)->notNull(),
            'month' => $this->integer(2)->notNull(),
        ]);

        $this->createTable('extra_fee_year', [
            'id' => $this->primaryKey(),
            'extraFeeId' => $this->integer(10)->notNull(),
            'year' => $this->integer(4)->notNull(),
        ]);

        $this->createTable('extra_fee_weekday', [
            'id' => $this->primaryKey(),
            'extraFeeId' => $this->integer(10)->notNull(),
            'weekday' => $this->integer(1)->notNull(),
        ]);

    }

    /**
     * @return bool
     */
    public function down()
    {
        $this->dropTable('extra_fee');
        $this->dropTable('extra_fee_time');
        $this->dropTable('extra_fee_day');
        $this->dropTable('extra_fee_month');
        $this->dropTable('extra_fee_year');
        $this->dropTable('extra_fee_weekday');
        return true;
    }
}
