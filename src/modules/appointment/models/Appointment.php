<?php

namespace reinvently\ondemand\core\modules\appointment\models;

use reinvently\ondemand\core\components\model\CoreModel;
use reinvently\ondemand\core\components\transport\ApiInterface;
use reinvently\ondemand\core\components\transport\ApiTransportTrait;

/**
 * This is the model class for table "appointment".
 *
 * @property string $id
 * @property string $startAt
 * @property string $finishAt
 * @property string $createdAt
 * @property string $updatedAt
 */
class Appointment extends CoreModel implements ApiInterface
{
    use ApiTransportTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'appointment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['startAt', 'finishAt'], 'required'],
            [['startAt', 'finishAt', 'createdAt', 'updatedAt'], 'integer'],
            ['startAt', 'validateIntersectionTimeIntervals'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'startAt' => 'Start At',
            'finishAt' => 'Finish At',
            'createdAt' => 'Created At',
            'updatedAt' => 'Updated At',
        ];
    }

    public function beforeSave($insert)
    {
        if ($this->getIsNewRecord()) {
            $this->createdAt = time();
        }
        if ($this->getDirtyAttributes()) {
            $this->updatedAt = time();
        }
        return parent::beforeSave($insert);
    }

    public function validateIntersectionTimeIntervals($attribute)
    {
        if (static::find()
            ->where(['<', 'startAt', $this->finishAt])
            ->andWhere(['>', 'finishAt', $this->startAt])
            ->exists()
        ) {
            $this->addError($attribute, 'Intersection of Time Intervals');
        }
    }

    /**
     * @return array
     */
    public function getItemForApi()
    {
        return [
            'id' => $this->id,
            'startAt' => $this->startAt,
            'finishAt' => $this->finishAt,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }

    /**
     * @return array
     */
    public function getItemShortForApi()
    {
        return $this->getItemForApi();
    }

}
