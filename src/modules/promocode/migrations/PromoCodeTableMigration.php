<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\promocode\migrations;

use reinvently\ondemand\core\modules\promocode\models\PromoCode;
use yii\db\Migration;

/**
 * Class PromoCodeTableMigration
 * @package reinvently\ondemand\core\modules\promocode\migrations
 */
class PromoCodeTableMigration extends Migration
{
    /**
     * Add PromoCode Table
     *
     * @return boolean return a false value to indicate the migration fails
     * and should not proceed further. All other return values mean the migration succeeds.
     */
    public function up()
    {
        $this->createTable(PromoCode::tableName(), [
            'id' => $this->primaryKey(),
            'code' => $this->string()->notNull(),
            'label' => $this->string(),
            'description' => $this->text(),
            'type' => $this->integer()->notNull()->unsigned(),
            'userId' => $this->integer()->notNull()->unsigned(),
            'isPercent' => $this->boolean()->notNull(),
            'percent' => $this->integer()->notNull(),
            'static' => $this->integer()->notNull(),
            'minPrice' => $this->integer()->notNull()->unsigned(),
            'usedNumbers' => $this->integer()->notNull()->unsigned(),
            'maxNumbers'=> $this->integer()->notNull()->unsigned(),
            'startAt' => $this->integer()->unsigned(),
            'expireAt' => $this->integer()->unsigned(),
            'createdAt' => $this->integer()->unsigned()->notNull(),
            'updatedAt' => $this->integer()->unsigned()->notNull(),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci');

        $this->createIndex('code', PromoCode::tableName(), 'code', true);
        $this->createIndex('userId', PromoCode::tableName(), 'userId');

        return true;
    }

    /**
     * @return bool
     */
    public function down()
    {
        $this->dropTable('promo_code');
        return true;
    }
    
}