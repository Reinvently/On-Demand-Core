<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\settings\models\extrafee;

use reinvently\ondemand\core\components\model\CoreModel;
use reinvently\ondemand\core\components\transport\ApiInterface;
use reinvently\ondemand\core\components\transport\ApiTransportTrait;
use reinvently\ondemand\core\modules\settings\models\extrafee\sets\ExtraFeeDatesSet;
use reinvently\ondemand\core\modules\settings\models\extrafee\virtual\ExtraFeeDates;
use reinvently\ondemand\core\modules\settings\models\Tariff;

/**
 * Class ExtraFee
 * @package reinvently\ondemand\core\modules\settings\models
 *
 * @property int id
 * @property int tariffId
 * @property string title
 * @property string description
 *
 */
class ExtraFee extends CoreModel implements ApiInterface
{
    use ApiTransportTrait;

    /**
     * @var ExtraFeeDates dates
     */
    private $dates;

    /**
     * ExtraFee constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        /** @var ExtraFeeDates dates */
        $this->dates = new ExtraFeeDates;
        parent::__construct($config);
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['tariffId', 'title'], 'required'],
            [['tariffId'], 'integer'],
            [['description'], 'safe'],
        ];
    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    static public function getExtraFees()
    {
        return ExtraFee::find()->joinWith([
            'extraFeeYear',
            'extraFeeMonth',
            'extraFeeWeekday',
            'extraFeeDay',
            'extraFeeTime',
            'tariff'
        ])->all();
    }

    /**
     * @return ExtraFeeDatesSet
     */
    public function getDates()
    {
        return new ExtraFeeDatesSet($this);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExtraFeeTime()
    {
        return $this->hasOne(ExtraFeeTime::class, ['extraFeeId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExtraFeeYear()
    {
        return $this->hasMany(ExtraFeeYear::class, ['extraFeeId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExtraFeeMonth()
    {
        return $this->hasMany(ExtraFeeMonth::class, ['extraFeeId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExtraFeeWeekday()
    {
        return $this->hasMany(ExtraFeeWeekday::class, ['extraFeeId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExtraFeeDay()
    {
        return $this->hasMany(ExtraFeeDay::class, ['extraFeeId' => 'id']);
    }

    /**
     * @return $this
     */
    public function getTariff()
    {
        return $this->hasOne(Tariff::class, ['id' => 'tariffId']);
    }

    /**
     * @param null $attribute
     * @return array
     */
    public function getErrors($attribute = null)
    {
        $errors = parent::getErrors($attribute);
        return array_merge($errors, $this->dates->getErrors());
    }

    /**
     * @param array $values
     * @param bool $safeOnly
     */
    public function setAttributes($values, $safeOnly = true)
    {
        $this->dates->setDates($values);
        parent::setAttributes($values, $safeOnly);
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        $this->dates->setExtraFeeId($this->id);
        $this->dates->save();

        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @return bool
     */
    public function beforeDelete()
    {
        $this->dates->setExtraFeeId($this->id);
        $this->dates->delete();

        return parent::beforeDelete();
    }

    /**
     * @param null $attributeNames
     * @param bool $clearErrors
     * @return bool
     * @throws \reinvently\ondemand\core\exceptions\ExtraFeeDatesNotSetException
     */
    public function validate($attributeNames = null, $clearErrors = true)
    {
        return $this->dates->validate() and parent::validate($attributeNames, $clearErrors);
    }

    /**
     * @param bool $runValidation
     * @param null $attributeNames
     * @return bool|int
     * @throws \Exception
     */
    public function update($runValidation = true, $attributeNames = null)
    {
        if (!$runValidation or $this->validate()) {
            $this->dates->setExtraFeeId($this->id);
            $this->dates->delete();

            return parent::update(false, $attributeNames);
        }
        return false;
    }


    /**
     * @return array
     */
    public function getItemForApi()
    {
        return [
            'id' => $this->id,
            'tariffId' => $this->tariffId,
            'title' => $this->title,
            'description' => $this->description,
        ];
    }

    /**
     * @return array
     */
    public function getItemShortForApi()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
        ];
    }
}