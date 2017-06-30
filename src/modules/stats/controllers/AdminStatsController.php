<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\stats\controllers;

use reinvently\ondemand\core\components\helpers\DateHelper;
use reinvently\ondemand\core\controllers\admin\AdminController;
use reinvently\ondemand\core\modules\stats\models\OrderFilterForm;
use app\models\Order;
use yii\helpers\ArrayHelper;

/**
 * Class AdminStatsController
 * @package reinvently\ondemand\core\modules\stats\controllers
 */
class AdminStatsController extends AdminController
{
    /**
     * @return string
     */
    public function actionOrder()
    {
        $formModel = new OrderFilterForm();

        $ordersArray = Order::find()
            ->select('userId')
            ->groupBy('userId')
            ->asArray()
            ->all();
        $userClass = \Yii::$app->user->identityClass;
        $users = $userClass::find()
            ->select(['id', 'firstName', 'lastName', 'email'])
            ->where(['id' => ArrayHelper::getColumn($ordersArray, 'userId')])
            ->asArray()
            ->all();
        $users = ArrayHelper::map($users, 'id', function ($el) {
            return $el['firstName'] . ' ' . $el['lastName'] . ' (ID: ' . $el['id'] . ', ' . $el['email'] . ')';
        });

        $xAxis = [];
        if ($formModel->load(\Yii::$app->request->post()) and $formModel->validate()) {
            $xAxisDates = DateHelper::dateRange($formModel->dateStart, $formModel->dateFinish, '+1 day', 'j M Y');
            if ($xAxisDates) {
                $orders = Order::find()
                    ->select([
                        'id',
                        'updatedAt',
                        'DATE_FORMAT(FROM_UNIXTIME(`updatedAt`), "%e %b %Y") AS "date_formatted"',
                        'count(*) as count',
                    ])
                    ->where(['userId' => $formModel->userId])
                    ->groupBy('date_formatted')
                    ->asArray()
                    ->all();
                $orders = ArrayHelper::index($orders, 'date_formatted');

                foreach ($xAxisDates as $date) {
                    $xAxis[$date] = isset($orders[$date]) ? (int)$orders[$date]['count'] : 0;
                }
            }
        }

        $data = [
            'formModel' => $formModel,
            'users' => ['' => '- Select user -'] + $users,
            'xAxis' => $xAxis,
        ];
        return $this->render('@app/core/modules/stats/views/admin/stats/order', $data);
    }
}