<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\user\strategies\traits;

use reinvently\ondemand\core\modules\role\models\Role;

/**
 * Created by PhpStorm.
 * User: eugene
 * Date: 2/12/16
 * Time: 17:55
 *
 */
trait Driver
{
    /** @var  Role $roleModelClass */
    public static $roleModelClass = Role::class;

    /**
     * @inheritDoc
     */
    public static function find()
    {
        $query = parent::find()
            ->andWhere(['roleId' => self::getRole()]);
        return $query;
    }

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        $this->roleId = self::getRole();
    }

    /**
     * @return mixed
     */
    private static function getRole()
    {
        $roleModelClass = self::$roleModelClass;
        return $roleModelClass::DRIVER;
    }
}