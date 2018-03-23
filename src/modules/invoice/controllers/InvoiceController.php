<?php
/**
 * @copyright Reinvently (c) 2018
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

/**
 * Created by PhpStorm.
 * User: sglushko
 * Date: 20.03.2018
 * Time: 14:26
 */

namespace reinvently\ondemand\core\modules\invoice\controllers;

use reinvently\ondemand\core\controllers\rest\ApiController;
use reinvently\ondemand\core\modules\invoice\models\Invoice;
use yii\web\ForbiddenHttpException;

class InvoiceController extends ApiController
{
    public $modelClass = Invoice::class;

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create'], $actions['update'], $actions['delete']);

        return $actions;
    }

    public function actionCreate()
    {
        throw new ForbiddenHttpException();
    }

    public function actionUpdate()
    {
        throw new ForbiddenHttpException();
    }

    public function actionDelete()
    {
        throw new ForbiddenHttpException();
    }

}