<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\map\assets;

use yii\web\AssetBundle;

/**
 * Class MapAsset
 * @package reinvently\ondemand\core\modules\map\assets
 */
class MapAsset extends AssetBundle
{
    public $sourcePath = '@app/core/modules/map/assets';

    public $css = [
        'css/adminMap.css',
    ];

    public $js = [
        'js/adminMap.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
