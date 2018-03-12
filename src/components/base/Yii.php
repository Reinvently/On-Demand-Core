<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */
/**
 * Created by PhpStorm.
 * User: sglushko
 * Date: 15.09.2015
 * Time: 16:19
 */

require(__DIR__ . '/../../../../../../vendor/yiisoft/yii2/BaseYii.php');

/**
 * Yii is a helper class serving common framework functionalities.
 *
 * It extends from [[\yii\BaseYii]] which provides the actual implementation.
 * By writing your own Yii class, you can customize some functionalities of [[\yii\BaseYii]].
 *
 * @property $app \yii\console\Application|\yii\web\Application|\reinvently\ondemand\core\components\base\Application
 */
class Yii extends \yii\BaseYii
{
    /** @var \reinvently\ondemand\core\components\base\Application */
    public static $app;


}

spl_autoload_register(['Yii', 'autoload'], true, true);
Yii::$classMap = require(YII2_PATH . '/classes.php');
Yii::$container = new \yii\di\Container();