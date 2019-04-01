<?php
/**
 * Created by PhpStorm.
 * User: sglushko
 * Date: 23.01.2019
 * Time: 10:46
 */

namespace reinvently\ondemand\core\components\loggers\models;


use yii\base\Component;

class LogFilter extends Component
{
    const FILTER_MASK = '******';

    static $attributes;
    static $patterns;
    static $replacements;

    /**
     * @return mixed
     */
    public static function getAttributes()
    {
        if (static::$attributes) {
            return static::$attributes;
        }

        static::$attributes = array_unique(array_merge(
            ['password', 'newPassword'],
            static::getAdditionalAttributes()
        ));

        return static::$attributes;
    }

    /**
     * @return mixed
     */
    public static function getAdditionalAttributes()
    {
        return [];
    }


    static public function process($string)
    {
        if (!static::$patterns || !static::$replacements) {
            static::$patterns = [];
            static::$replacements = [];
            foreach (static::getAttributes() as $attribute) {
                static::$patterns[] = '/"' . $attribute . '" *: *"(?:\\"|[^"])*?"/';
                static::$replacements[] = '"' . $attribute . '": "' . static::FILTER_MASK . '"';
            }
        }

        if (!static::$patterns || !static::$replacements) {
            return true;
        }

        return preg_replace(static::$patterns, static::$replacements, $string);
    }

}