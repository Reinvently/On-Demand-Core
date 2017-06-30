<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\settings\models\extrafee\virtual;

use reinvently\ondemand\core\modules\settings\models\extrafee\ExtraFeeDay;

/**
 * Class ExtraFeeMonthdays
 * @package reinvently\ondemand\core\modules\settings\models\extrafee\virtual
 */
class ExtraFeeMonthdays implements ExtraFeeVirtualModel
{
    private $days;
    private $extraFeeId;
    private $errors = [];

    /**
     * ExtraFeeMonthdays constructor.
     * @param array $days
     */
    public function __construct(Array $days)
    {
        $this->days = $days;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param int $id
     */
    public function setExtraFeeId($id)
    {
        $this->extraFeeId = $id;
    }

    /**
     * @return bool
     */
    public function validate()
    {
        foreach ($this->days as $day) {
            /** @var ExtraFeeDay $dayEntity */
            $dayEntity = new ExtraFeeDay();
            $dayEntity->day = $day;

            if (!$dayEntity->validate()) {
                $this->addErrors($dayEntity->getErrors());
                return false;
            }
        }

        return true;
    }

    /**
     * @throws \yii\db\Exception
     */
    public function save()
    {
        $mapToAttributes = function ($day) {
            return [
                'extraFeeId' => $this->extraFeeId,
                'day' => $day
            ];
        };

        $rows = array_map($mapToAttributes, $this->days);

        \Yii::$app->db->createCommand()->batchInsert(
            ExtraFeeDay::tableName(),
            ['extraFeeId', 'day'],
            $rows
        )->execute();
    }

    /**
     *
     */
    public function delete()
    {
        ExtraFeeDay::deleteAll(["extraFeeId" => $this->extraFeeId]);
    }

    /**
     * @param $errorsList
     */
    private function addErrors($errorsList)
    {
        foreach ($errorsList as $key => $error) {
            $this->addError($key, $error);
        }
    }

    /**
     * @param $key
     * @param $error
     */
    private function addError($key, $error)
    {
        if (!isset($this->errors[$key])) {
            $this->errors[$key] = [$error];
        } else {
            $this->errors[$key][] = $error;
        }
    }
}