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

/**
 * Class ExtraFeeTime
 * @package reinvently\ondemand\core\modules\settings\models\extrafee
 *
 * @property int id
 * @property int extraFeeId
 * @property string timeStart
 * @property string timeFinish
 *
 */
class ExtraFeeTime extends CoreModel implements ApiInterface
{
    use ApiTransportTrait;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['extraFeeId'], 'integer'],
            [['timeStart', 'timeFinish'], 'validateTime'],
        ];
    }

    /**
     * @param $attribute
     * @param $param
     */
    public function validateTime($attribute, $param)
    {
        $time = $this->getAttribute($attribute);

        if(!strtotime($time)) {
            $this->addError($attribute, \Yii::t('yii', 'Incorrect time format'));
        }
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if($this->timeStart) {
            $this->timeStart = (new \DateTime($this->timeStart))->format("H:i:s");
        }

        if($this->timeFinish) {
            $this->timeFinish = (new \DateTime($this->timeFinish))->format("H:i:s");
        }

        return parent::beforeSave($insert);
    }


    /**
     * @return array
     */
    public function getItemForApi()
    {
        return [
            'id' => $this->id,
            'extraFeeId' => $this->extraFeeId,
            'timeStart' => $this->timeStart,
            'timeFinish' => $this->timeFinish,
        ];
    }

    /**
     * @return array
     */
    public function getItemShortForApi()
    {
        return [
            'id' => $this->id,
            'extraFeeId' => $this->extraFeeId,
            'timeStart' => $this->timeStart,
            'timeFinish' => $this->timeFinish,
        ];
    }
}