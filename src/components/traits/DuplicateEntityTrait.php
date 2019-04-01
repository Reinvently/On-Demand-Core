<?php
/**
 * @copyright Reinvently (c) 2018
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\components\traits;
use reinvently\ondemand\core\components\loggers\models\ExceptionLog;
use yii\db\ActiveRecord;
use yii\db\IntegrityException;

/**
 * Trait DuplicateEntityTrait
 * @package reinvently\ondemand\core\components\traits
 *
 * @mixin ActiveRecord
 */
trait DuplicateEntityTrait
{
    /**
     * @inheritdoc
     */
    public function save($runValidation = true, $attributeNames = null)
    {
        if (!$this->getIsNewRecord()) {
            return parent::save($runValidation, $attributeNames);
        }


        $duplicateSqlErrorCode = 1062; /* ER_DUP_ENTRY */
        try {
            return parent::save($runValidation, $attributeNames);
        } catch (IntegrityException $e) {
            if (isset($e->errorInfo[1]) && $e->errorInfo[1] === $duplicateSqlErrorCode) {
                $entity = static::findOne($this->getPrimaryKey());
                $entity->setAttributes($this->getAttributes(), false);
                $result = $entity->save($runValidation, $attributeNames);
//                ExceptionLog::saveException(new \Exception('DuplicateEntityTrait result:' . $result
//                    . ' class:' . static::class
//                    . ' attributes:' . var_export($this->getAttributes(), true)));
                if ($result) {
                    $this->setOldAttributes($this->getAttributes());
                } else {
                    $this->addErrors($entity->getErrors());
                }
                return $result;
            }
            throw $e;
        }
    }

}