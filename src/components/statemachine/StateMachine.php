<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\components\statemachine;

use reinvently\ondemand\core\components\eventmanager\EventInterface;
use reinvently\ondemand\core\components\statemachine\exceptions\InvalidStateException;
use reinvently\ondemand\core\modules\stats\StatsInterface;
use yii;
use yii\base\Behavior;

/**
 * Class StateMachine
 * @package reinvently\ondemand\core\components\statemachine
 *
 * @property StateMachineModel owner
 */
class StateMachine extends Behavior implements StatsInterface, EventInterface
{
    const EVENT_BEFORE_EXIT = 'BeforeExit';
    const EVENT_AFTER_ENTER = 'AfterEnter';

    /** @var yii\base\Component|StatsInterface|StateMachineModel */
    public $owner;
    public $defaultStateName = 'default';
    public $enableTransitionHistory = false;
    public $checkTransitionMap = false;

    protected $_transitionHistory;
    protected $_stateName;
    protected $_columnName;

    /** @var State[] */
    protected $_states = [];
    protected $_isInitialized = false;
    protected $_uniqueID;

    private $_error;

    public function init()
    {
        $this->_isInitialized = true;
    }

    /**
     * Attaches state behaviors
     *
     * @param yii\base\Component $owner
     */
    public function attach($owner)
    {
        parent::attach($owner);
        if (!$this->_uniqueID) {
            $this->_uniqueID = uniqid();
        }

        if (($state = $this->getState()) !== null) {
            // Ex. Order = $owner;
            $owner->attachBehavior($this->_uniqueID . '_' . $state->getName(), $state);
        }
    }

    /**
     * @return State|null
     */
    public function getState()
    {
        return $this->getStateByName($this->getStateName());
    }

    /**
     * @param string $name
     * @return State|null
     */
    public function getStateByName($name)
    {
        return isset($this->_states[$name]) ? $this->_states[$name] : null;
    }

    /**
     * @return string
     */
    public function getStateName()
    {
        return $this->_stateName ? $this->_stateName : $this->defaultStateName;
    }

    /**
     * Adding states to state machine
     *
     * @param mixed $states
     */
    protected function setStates($states)
    {
        $this->_states = [];
        foreach ($states as $state) {
            $this->addState($state);
        }

    }

    /**
     * Add state to state machine
     *
     * @param array $state
     * @return null|State
     */
    protected function addState($state)
    {
        if (is_array($state)) {
            if (!isset($state['class'])) {
                $state['class'] = 'State';
            }
            if (!isset($state['transitsTo'])) {
                $state['transitsTo'] = [];
            }
            $label = null;
            if (isset($state['label'])) {
                $label = $state['label'];
            }

            //Yii::createComponent($state, $state['name'], $this);
            $class = $state['class'];
            /** @var State $state */
            $state = new $class($state['name'], $this, $state['transitsTo']);
            $state->setLabel($label);

            return $this->_states[$state->getName()] = $state;

        }
        return null;
    }

    /**
     * Set current state name
     *
     * @param string $name
     *
     * @TODO not for manual set, improve method in future
     *
     */
    public function setStateName($name)
    {
        $this->_stateName = $name;
    }

    public function setColumnName($name)
    {
        $this->_columnName = $name;
    }

    public function getColumnName()
    {
        return $this->_columnName;
    }

    /**
     * @param string $name
     * @return mixed
     * @throws yii\base\UnknownPropertyException
     */
    /*public function __get($name)
    {
        $state = $this->getState();
        if ($state !== null && (property_exists($state, $name) || $state->canGetProperty($name))) {
            return $state->{$name};
        }
        return parent::__get($name);
    }*/

    /**
     * Do transition
     *
     * @param string $to
     * @param array $params
     * @return bool
     * @throws InvalidStateException
     */
    public function transition($to, $params = null)
    {
        $owner = $this->owner;
        if (!$this->hasState($owner->getStatus())) {
            throw new InvalidStateException();
        }

//        if (!$this->hasState($to)) {
//            throw new InvalidStateException('No such state: ' . $to);
//        }

        if (!$this->canTransit($to, $params)) {
            $this->_error = "Cannot transit to state: " . $to;
            return false;
        }

        $toState = $this->_states[$to];
        $fromState = $this->getState();

        if ($owner !== null) {
            // Attach current state to the owner
            $owner->detachBehavior($this->_uniqueID . '_' . $this->getStateName());
            $this->setStateName($to);

            //$owner->updateAttributes([$this->_columnName => $to]);
            $owner->{$this->_columnName} = $to;
            $owner->save(false, [$this->_columnName]);

            $owner->attachBehavior($this->_uniqueID . '_' . $to, $toState);
        } else {
            $this->setStateName($to);
        }

        if ($this->enableTransitionHistory) {
            /** @TODO history */
        }

        $this->afterTransition($fromState, $params);

        return true;
    }

    /**
     * Check if state exists
     *
     * @param string $state
     * @return bool
     */
    protected function hasState($state)
    {
        return isset($this->_states[$state]);
    }

    /**
     * Check transition availability
     *
     * @param string $to
     * @param null|array $params
     * @return bool
     * @throws InvalidStateException
     */
    protected function canTransit($to, $params = null)
    {
        if (!$this->hasState($to)) {
            throw new InvalidStateException('No such state: ' . $to);
        }

        $toState = $this->_states[$to];
        if (!$this->beforeTransition($toState, $params)) {
            return false;
        }

        return true;
    }

    /**
     * @param State $toState
     * @param null $params
     * @return bool
     */
    protected function beforeTransition(State $toState, $params = null)
    {
        if (!$this->getState()->beforeExit($toState, $params) || !$toState->beforeEnter($toState, $params)) {
            return false;
        }

//        $transition = new StateTransition();
//        $transition->from = $this->getState();
//        $transition->to = $toState;
//        /** @TODO raise transition event */
        Yii::$app->eventManager->call(static::EVENT_BEFORE_EXIT, $this);

        return true;
    }

    /**
     * @param State $fromState
     * @param null $params
     */
    protected function afterTransition(State $fromState, $params = null)
    {
        $fromState->afterExit();
        $this->getState()->afterEnter($fromState, $params);

//        $transition = new StateTransition();
//        $transition->from = $fromState;
//        $transition->to = $this->getState();
//        /** @TODO raise transition event */
        Yii::$app->eventManager->call(static::EVENT_AFTER_ENTER, $this);

    }

    /** @return \stdClass short data for stats */
    public function getStatsObject()
    {
        $o = new \stdClass();
        $o->owner = $this->owner->getStatsObject();
        return $o;

    }

    public function getApiStateList()
    {
        $json = [
            'states' => [],
            'stateMachine' => [],
            'defaultStateName' => $this->defaultStateName,
        ];
        foreach($this->_states as $state) {
            $json['states'][$state->getName()] = $state->getLabel();
            $json['stateMachine'][$state->getName()] = $state->getTransitsTo();
        }
        return $json;
    }

    public function getApiState()
    {
        return [
            'state' => (int) $this->getStateName()
        ];
    }

    public function getStateMachineError()
    {
        return $this->_error;
    }

    /**
     * @return void|array Events list for ex. [Test2::RAISE_EVENT_TEST2];
     */
    public static function requiredTriggeredEvent()
    {
    }

    /**
     * @return array Events list for ex. [Test2::RAISE_EVENT_TEST2];
     */
    public static function optionalTriggeredEvent()
    {
        return [
            static::EVENT_BEFORE_EXIT,
            static::EVENT_AFTER_ENTER
        ];
    }

    /**
     * @return void|array Events list for ex. [Test2::RAISE_EVENT_TEST2];
     */
    public static function requiredOnEvent()
    {
    }

    /**
     * @return void|array Events list for ex. [Test2::RAISE_EVENT_TEST2];
     */
    public static function optionalOnEvent()
    {
    }
}