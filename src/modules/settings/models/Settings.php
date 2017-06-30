<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\modules\settings\models;


use reinvently\ondemand\core\components\model\CoreModel;

/**
 * Class Settings
 * @package reinvently\ondemand\core\modules\settings\models
 *
 * @property int id
 * @property string key
 * @property string value
 *
 */
class Settings extends CoreModel
{
    private static $_cache = [];

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['key'], 'required'],
            [['value'], 'safe'],
        ];
    }

    public static function get($key)
    {
        if (!static::$_cache) {
            $settings = static::find()->all();
            foreach ($settings as $s) {
                static::$_cache[$s->key] = $s->value;
            }
        }

        return isset(static::$_cache[$key]) ? static::$_cache[$key] : null;
    }

    public static function set($key, $value)
    {
        $settings = static::findOne(['key' => $key]);
        if (!$settings) {
            $settings = new static;
            $settings->key = $key;
        }
        $settings->value = $value;
        $settings->save();
    }

}