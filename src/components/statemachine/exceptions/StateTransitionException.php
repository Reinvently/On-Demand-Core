<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\components\statemachine\exceptions;


class StateTransitionException extends StateException
{
    public function __construct($message = "Cannot transit", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}