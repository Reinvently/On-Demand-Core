<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\components\statemachine;

use yii\base\Event;

class StateTransition extends Event
{
    public $from;
    public $to;
    public $isValid = true;

}