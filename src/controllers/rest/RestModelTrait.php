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


use yii\base\Model;
use yii\db\BaseActiveRecord;

/**
 * Trait RestModelTrait
 * @package reinvently\ondemand\core\controllers\rest
 *
 * @mixin Model
 */
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

    /**
     * @inheritDoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        if (!key_exists(ApiController::CREATE_SCENARIO, $scenarios)) {
            $scenarios[ApiController::CREATE_SCENARIO] = $scenarios[self::SCENARIO_DEFAULT];
        }
        if (!key_exists(ApiController::UPDATE_SCENARIO, $scenarios)) {
            $scenarios[ApiController::UPDATE_SCENARIO] = $scenarios[self::SCENARIO_DEFAULT];
        }
        return $scenarios;
    }

}