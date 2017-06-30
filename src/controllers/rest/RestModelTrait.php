<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */
/**
 * Created by PhpStorm.
 * User: sglushko
 * Date: 31.01.2016
 * Time: 21:57
 */

namespace reinvently\ondemand\core\controllers\rest;


use yii\db\BaseActiveRecord;

trait RestModelTrait
{
    /**
     * @return \yii\db\ActiveQueryInterface
     */
    public function searchFind()
    {
        /** @var BaseActiveRecord $this */
        return $this->find();
    }

}