<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\components\model;

use reinvently\ondemand\core\controllers\rest\RestModelTrait;
use yii\db\ActiveRecord;

abstract class CoreModel extends ActiveRecord
{
    use RestModelTrait;
}