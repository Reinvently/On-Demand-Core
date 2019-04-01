<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\settings\models\extrafee;

use yii\db\ActiveRecord;

/**
 * Class ExtraFeeYear
 * @package reinvently\ondemand\core\modules\settings\models\extrafee
 *
 * @property int id
 * @property int extraFeeId
 * @property int year
 */
class ExtraFeeYear extends ActiveRecord
{
    /**
     * @return string
     */
    static public function tableName()
    {
        return 'extra_fee_year';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [["extraFeeId"], "required", "on" => ['backend.edit', 'backend.add']],
            ["year", 'compare', 'compareValue' => date("Y"), 'operator' => '>='],
            ["year", 'compare', 'compareValue' => (new \DateTime())->modify("+100 years")->format("Y"), 'operator' => '<'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'year' => 'Year',
        ];
    }
}