<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\map\controllers;

use reinvently\ondemand\core\controllers\admin\AdminController;

/**
 * Class AdminMapController
 * @package reinvently\ondemand\core\modules\map\controllers
 */
class AdminMapController extends AdminController
{
    /**
     * @return string
     */
    public function actionLocations()
    {
        return $this->render('@app/core/modules/map/views/map/locations', []);
    }
}