<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\components\statemachine;

use reinvently\ondemand\core\components\statemachine\exceptions\InvalidValueException;
use yii\base\Behavior;

class State extends Behavior
{
    protected $_machine;
    protected $_name;
    protected $_transitsTo = [];

    public function __construct($name, StateMachine $owner, $transitsTo = [])
    {
        $this->setName($name);
        $this->setMachine($owner);
        $this->setTransitsTo($transitsTo);
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->_name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @param StateMachine $machine
     */
    public function setMachine(StateMachine $machine)
    {
        $this->_machine = $machine;
    }

    /**
     * @return StateMachine
     */
    public function getMachine()
    {
        return $this->_machine;
    }

    /**
     * @param mixed $states
     * @throws InvalidValueException
     */
    public function setTransitsTo($states)
    {
        if (!is_array($states)) {
            throw new InvalidValueException('"transitsTo" states param must be array');
        }
        $this->_transitsTo = $states;
    }

    public function getTransitsTo()
    {
        return $this->_transitsTo;
    }


    public function beforeEnter()
    {
        return true;
    }

    /**
     * @param State $toState
     * @return bool
     */
    public function beforeExit(State $toState)
    {
        // Check if current state can transit to $toState
        if ($this->_machine->checkTransitionMap and !in_array($toState->getName(), $this->_transitsTo)) {
            return false;
        }
        return true;
    }

    public function afterEnter(State $from)
    {
        return true;
    }

    public function afterExit()
    {
        return true;
    }


}