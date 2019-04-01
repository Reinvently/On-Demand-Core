<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\settings\models\extrafee;

use yii\db\ActiveRecord;

/**
 * Class ExtraFeeMonth
 * @package reinvently\ondemand\core\modules\settings\models\extrafee
 *
 * @property int id
 * @property int extraFeeId
 * @property int month
 */
class ExtraFeeMonth extends ActiveRecord
{
    /**
     * @return string
     */
    static public function tableName()
    {
        return 'extra_fee_month';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [["extraFeeId"], "required", "on" => ['backend.edit', 'backend.add']],
            [["month"], 'in', 'range' => range(1, 12)],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'month' => 'Month',
        ];
    }
}