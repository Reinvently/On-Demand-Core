<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\settings\assets;

use yii\web\AssetBundle;

/**
 * Class ExtraFeeAsset
 * @package reinvently\ondemand\core\modules\order\assets
 */
class ExtraFeeAsset extends AssetBundle
{
    public $sourcePath = '@app/core/modules/settings/assets';

    public $css = [
    ];

    public $js = [
        'js/moment.js',
        'js/moment-timezone-with-data-2010-2020.js',
        'js/form.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
