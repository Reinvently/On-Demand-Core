<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\order\assets;

use yii\web\AssetBundle;

/**
 * Class OrderAsset
 * @package reinvently\ondemand\core\modules\order\assets
 */
class OrderAsset extends AssetBundle
{
    public $sourcePath = '@app/core/modules/order/assets';

    public $css = [
    ];

    public $js = [
        'js/adminOrder.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
