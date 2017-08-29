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
    /** @var StateMachine */
    protected $_machine;
    protected $_name;
    protected $_transitsTo = [];

    /**
     * State constructor.
     * @param string $name
     * @param StateMachine $owner
     * @param array $transitsTo
     * @param array $config
     */
    public function __construct($name, StateMachine $owner, $transitsTo = [], array $config = [])
    {
        $this->setName($name);
        $this->setMachine($owner);
        $this->setTransitsTo($transitsTo);
        parent::__construct($config);
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

    /**
     * @param State $toState
     * @param null $params
     * @return bool
     */
    public function beforeEnter(State $toState, $params = null)
    {
        if (!$toState || !(is_array($params) || $params === null)) {
            return false;
        }

        return true;
    }

    /**
     * @param State $toState
     * @param null $params
     * @return bool
     */
    public function beforeExit(State $toState, $params = null)
    {
        // Check if current state can transit to $toState

        if (!$this->getMachine()->checkTransitionMap) {
            return true;
        }

        if (in_array($toState->getName(), $this->getTransitsTo())) {
            return true;
        }

        $roleModelClass = $this->getMachine()->owner->roleModelClass;
        $role = $roleModelClass::SYSTEM;
        if (key_exists('user', $params)) {
            $role = $params['user'];
        }

        foreach ($this->getTransitsTo() as $transit) {
            if (isset($transit[0]) && isset($transit[1])) {
                list($roles, $transitTo) = $transit;
                if (
                    $toState->getName() == $transitTo
                    && in_array($role, $roles)
                ) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param State $fromState
     * @param null $params
     * @return bool
     */
    public function afterEnter(State $fromState, $params = null)
    {
        if (!$fromState || !(is_array($params) || $params === null)) {
            return false;
        }

        return true;
    }

    public function afterExit()
    {
        return true;
    }


}