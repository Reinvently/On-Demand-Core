<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel reinvently\ondemand\core\modules\payment\models\PaymentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Payment';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            'id',
            [
                'label' => 'Status',
                'attribute' => 'status',
                'filter' => \reinvently\ondemand\core\modules\payment\models\Payment::$statuses,
                'format' => 'raw',
                'value' => function ($data) {
                    return \reinvently\ondemand\core\modules\payment\models\Payment::$statuses[$data->status];
                },
            ],
            [
                'label' => 'Order',
                'attribute' => 'orderId',
                'format' => 'raw',
                'value' => function ($data) {
                    return $data->orderId;
                }
            ],
            'price',
            'transactionId',
            'description',
            // 'v',
            [
                'attribute' => 'createdAt',
                'format' => 'datetime',
            ],
            // 'updatedAt',

            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
