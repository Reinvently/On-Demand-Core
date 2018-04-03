<?php
/**
 * @copyright Reinvently (c) 2018
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

/**
 * Created by PhpStorm.
 * User: sglushko
 * Date: 02.04.2018
 * Time: 16:35
 */

namespace reinvently\ondemand\core\components\loggers\controllers;

use reinvently\ondemand\core\components\loggers\models\ExceptionLog;
use reinvently\ondemand\core\controllers\admin\AdminController;
use yii\data\ActiveDataProvider;
use yii\web\HttpException;

/**
 * Class ExceptionLogController
 * @package reinvently\ondemand\core\components\loggers\controllers
 */
class ExceptionLogController extends AdminController
{
    /**
     * @return string
     * @throws HttpException
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider(['query' => ExceptionLog::find()]);

        $dataProvider->setPagination([
            'pageSize' => 20,
            'totalCount' => $dataProvider->getTotalCount(),
        ]);

        $dataProvider->setSort([
            'defaultOrder' => ['id' => SORT_DESC],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);

    }


}