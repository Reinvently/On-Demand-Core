<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

/**
 * Created by PhpStorm.
 * User: sglushko
 * Date: 24.10.2017
 * Time: 11:22
 */

namespace reinvently\ondemand\core\vendor\tasker\daemon;


trait SingletonTrait
{
    /** @var $this */
    private static $_instance;


    private function __construct()
    {

    }

    private function __clone()
    {

    }

    private function __wakeup()
    {

    }

    /**
     * @return static
     */
    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new static();
        }
        return self::$_instance;
    }

}