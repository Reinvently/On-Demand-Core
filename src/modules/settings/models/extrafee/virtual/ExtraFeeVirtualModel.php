<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\settings\models\extrafee\virtual;

/**
 * Interface ExtraFeeVirtualModel
 * @package reinvently\ondemand\core\modules\settings\models\extrafee\virtual
 */
interface ExtraFeeVirtualModel
{
    /**
     * @param $id
     */
    public function setExtraFeeId($id);

    /**
     * @return mixed
     */
    public function getErrors();

    /**
     * @return mixed
     */
    public function validate();

    /**
     * @return mixed
     */
    public function save();

    /**
     * @return mixed
     */
    public function delete();
}