<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\stats\models;

class OrderFilterForm extends \yii\base\Model
{
    public $userId;
    public $dateStart;
    public $dateFinish;

    public function rules()
    {
        return [
            [['userId', 'dateStart', 'dateFinish'], 'required'],
            [['dateStart', 'dateFinish'], 'date', 'format' => 'yyyy-M-d'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'userId' => 'User',
        ];
    }
}