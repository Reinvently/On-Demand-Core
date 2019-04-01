<?php
/**
 * @copyright Reinvently (c) 2018
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\components\model;

use reinvently\ondemand\core\controllers\rest\RestModelTrait;
use yii\db\ActiveRecord;

abstract class CoreModel extends ActiveRecord
{
    use RestModelTrait;

    /**
     * @return string
     */
    final static public function className()
    {
        return static::class;
    }

    /**
     *
     */
    public function afterValidate()
    {
        $schema = static::getTableSchema();

        foreach ($this->attributes as $attribute => $value) {
            if (!$this->hasErrors($attribute)) {
                $typeCasted = $schema->getColumn($attribute)->phpTypecast($value);

                if ($value !== $typeCasted) {
                    $this->setAttribute($attribute, $typeCasted);
                }
            }
        }

        return parent::afterValidate();
    }

    public function unsafeAttributes()
    {
        return array_values(array_diff($this->attributes(), $this->safeAttributes()));
    }
}