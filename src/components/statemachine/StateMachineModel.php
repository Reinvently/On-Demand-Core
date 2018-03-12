<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\components\statemachine;

use reinvently\ondemand\core\components\model\CoreModel;
use reinvently\ondemand\core\components\statemachine\exceptions\StateTransitionException;
use reinvently\ondemand\core\components\transport\ApiInterface;
use reinvently\ondemand\core\modules\role\models\Role;
use reinvently\ondemand\core\modules\stats\StatsInterface;
use yii\db\StaleObjectException;


/**
 * Class StateMachineModel
 * @package reinvently\ondemand\core\components\statemachine
 *
 * @property string $defaultStateName
 *
 * @property int $v
 * @method string getColumnName() @see StateMachine
 * @method setStateName($name) @see StateMachine
 * @method bool transition(string $name, array $params = null) @see StateMachine
 * @method getApiState() @see StateMachine
 * @method getStateMachineError() @see StateMachine
 * @method getApiStateList() @see StateMachine
 * @method State getState() @see StateMachine
 * @method State getStateByName(string $name) @see StateMachine
 *
 */
abstract class StateMachineModel extends CoreModel implements StatsInterface, ApiInterface
{

    /** @var Role */
    public $roleModelClass = Role::class;

    abstract public function getStateMachineParams();

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['stateMachine'] = $this->getStateMachineParams();
        return $behaviors;
    }


    public function afterFind()
    {
        parent::afterFind();
        $this->setStateName($this->{$this->getColumnName()});
    }

    public function beforeValidate()
    {
        $column = $this->getColumnName();
        if (!$this->$column) {
            $this->$column = $this->defaultStateName;
        }
        return parent::beforeValidate();
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->v = 0;
        }
        return parent::beforeSave($insert);
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->{$this->getColumnName()};
    }

    /**
     * @param int $status
     * @return bool
     * @throws StateTransitionException
     */
    public function checkStatus($status)
    {
        if ($this->getStatus() == $status) {
            return true;
        }
        throw new StateTransitionException();
    }

    final public function optimisticLock()
    {
        return 'v';
    }

    /**
     * @param bool $runValidation
     * @param array $attributeNames
     * @return bool|int
     * @throws StaleObjectException
     * @throws \Exception
     */
    public function update($runValidation = true, $attributeNames = null)
    {
        try {
            return parent::update($runValidation, $attributeNames);
        } catch (StaleObjectException $e) {
            throw $e;
        }
    }

    /**
     * @return false|int
     * @throws StaleObjectException
     * @throws \Exception
     */
    public function delete()
    {
        try {
            return parent::delete();
        } catch (StaleObjectException $e) {
            throw $e;
        }
    }

    /** @return \stdClass short data for stats */
    public function getStatsObject()
    {
//        $column = $this->getColumnName();
//        $o = new \stdClass();
//        $o->$column = $this->$column;
//        return $o;
        return (object) $this->getItemForApi();
    }
}