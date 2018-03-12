<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\components\statemachine;

use reinvently\ondemand\core\components\statemachine\exceptions\InvalidValueException;
use reinvently\ondemand\core\modules\user\models\User;
use yii\base\Behavior;

class State extends Behavior
{
    /** @var StateMachine */
    protected $_machine;
    protected $_name;
    protected $_label;
    /** @var array [State1::_name, State2::_name, [[Role::id, ...], State3::_name]] */
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
     * @return string
     */
    public function getLabel()
    {
        return $this->_label;
    }

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->_label = $label;
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
     * @return array
     */
    public function getTransitions()
    {
        $transitions = [];
        foreach ($this->getTransitsTo() as $transit) {
            if (is_array($transit)) {
                if (isset($transit[0]) && isset($transit[1])) {
                    list(, $transitTo) = $transit;
                    $transitions[] = $transitTo;
                }
            } else {
                $transitions[] = $transit;
            }
        }
        return $transitions;
    }

    /**
     * @param $transitTo
     * @return array
     */
    public function getTransitRoles($transitTo)
    {
        foreach ($this->getTransitsTo() as $transit) {
            if (is_array($transit)) {
                if (isset($transit[0]) && isset($transit[1])) {
                    list($roles, $to) = $transit;
                    if ($to == $transitTo) {
                        return $roles;
                    }
                }
            }
        }
        return [];
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

        foreach ($this->getTransitions() as $transitTo) {
            if ($transitTo == $toState->getName()) {
                $roles = $this->getTransitRoles($transitTo);
                if (!$roles || in_array($this->getCurrentRole($params), $roles)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getCurrentRole($params) {
        $roleModelClass = $this->getMachine()->owner->roleModelClass;
        $role = $roleModelClass::SYSTEM;
        $user = $this->getParamsUser($params);
        if ($user) {
            $role = $user->roleId;
        }
        return $role;
    }

    /**
     * @param $params
     * @return User
     */
    public function getParamsUser($params) {
        if (
            is_array($params)
            && key_exists('user', $params)
            && $params['user'] instanceof User
        ) {
            /** @var User $user */
            return $params['user'];
        }
        return null;
    }

    /**
     * @param State $fromState
     * @param null $params
     */
    public function afterEnter(State $fromState, $params = null)
    {
    }

    /**
     *
     */
    public function afterExit()
    {
    }

}