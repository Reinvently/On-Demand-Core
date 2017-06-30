<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\promocode\migrations;

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
        $this->createTable('promo_code', [
            'id' => $this->primaryKey(),
            'code' => $this->string(255),
            'type' => $this->integer(2)->notNull()->unsigned(),
            'promoType' => $this->integer(2)->notNull()->unsigned(),
            'userId' => $this->integer(10)->notNull()->unsigned(),
            'amount' => $this->integer(10)->notNull()->unsigned(),
            'minAmount' => $this->integer(10)->unsigned()->defaultValue(0),
            'usedCount' => $this->integer(10)->unsigned()->notNull()->defaultValue(0),
            'days' => $this->string(255),
            'startAt' => $this->integer(10)->unsigned(),
            'expireAt' => $this->integer(10)->unsigned(),
            'createdAt' => $this->integer(10)->unsigned()->notNull(),
            'updatedAt' => $this->integer(10)->unsigned()->notNull(),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci');
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