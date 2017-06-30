<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\modules\stats\models;

use reinvently\ondemand\core\components\model\CoreMongoDbModel;
use Yii;

/**
 * Class SimpleStats
 * @package reinvently\ondemand\core\modules\stats\models
 *
 * @property int $userId
 * @property string $event
 * @property string $authKey
 * @property string $class
 * @property object $object
 *
 */
class Stats extends CoreMongoDbModel
{
    public static function collectionName()
    {
        return 'stats';
    }

    public function attributes()
    {
        return ['_id', 'event', 'userId', 'authKey', 'class', 'object'];
    }

} 