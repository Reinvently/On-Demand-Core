<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

/**
 * Created by PhpStorm.
 * User: sglushko
 * Date: 19.10.2017
 * Time: 13:45
 */

namespace reinvently\ondemand\core\vendor\tasker\daemon;

class Params
{
    const PARAMS_UPDATE_INTERVAL = 1;
    const PARAMS_NUMBERS_OF_TASKER = 2;
    const PARAMS_TASKER_SLEEP_INTERVAL = 3;

    /** @var array */
    protected static $params;

    /** @var int */
    protected static $timeNextUpdate;

    /** @var array */
    protected static $paramsDefault;
    
    /**
     * 
     */
    public static function init()
    {
        static::$paramsDefault = [
            [static::PARAMS_UPDATE_INTERVAL, 60],
            [static::PARAMS_NUMBERS_OF_TASKER, 2],
            [static::PARAMS_TASKER_SLEEP_INTERVAL, 5],
        ];

        static::update();
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function get($id)
    {
        static::checkUpdate();
        if (key_exists($id, static::$params)) {
            return static::$params[$id];
        }
        return null;
    }

    /**
     * 
     */
    protected static function checkUpdate()
    {
        if (static::$timeNextUpdate < time()) {
            static::update();
        }
    }

    /**
     * 
     */
    protected static function update()
    {
        $t = Tasker::getInstance();

        $res = $t->db->query(<<<SQL
SELECT id, `value`
FROM tasker_params
SQL
);

        static::$params = static::$paramsDefault;
        foreach ($res as $param) {
            static::$params[$param['id']] = $param['value'];
        }
        static::$timeNextUpdate = time() + static::$params[static::PARAMS_UPDATE_INTERVAL];
    }

}
