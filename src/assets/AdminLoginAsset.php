<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\assets;

use yii\web\AssetBundle;

class AdminLoginAsset extends AssetBundle
{
    public $sourcePath = '@app/core/assets';

    //public $basePath = '@webroot';
    //public $baseUrl = '@web';
    public $css = [
        'css/adminLogin.css',
    ];
    public $js = [
        //'js/adminLogin.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
