<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\modules\location\migrations;

use Yii;
use yii\db\Migration;

class PointIndexMigration extends Migration
{
    public function up()
    {
        $collection = Yii::$app->mongodb->getCollection('locationPoint');
        $collection->createIndex(['type' => 1, 'location' => "2dsphere"]);
        $collection->createIndex(['type' => 1, 'externalId' => -1], ['unique' => 1]);
        $collection->createIndex(['location' => "2dsphere"]);
    }

    public function down()
    {
        $collection = Yii::$app->mongodb->getCollection('locationPoint');
        $collection->dropAllIndexes();
    }
}