<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Orders';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Order', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            'id',
            [
                'label' => 'Status',
                'attribute' => 'status',
                'filter' => \app\models\Order::$statuses,
                'format' => 'raw',
                'value' => function ($data) {
                    return \app\models\Order::$statuses[$data->status];
                },
            ],
            [
                'label' => 'Address',
                'attribute' => 'addressId',
                'format' => 'raw',
                'value' => function ($data) {
                    return $data->address ? $data->address->address : '---';
                }
            ],
            [
                'label' => 'User',
                'format' => 'raw',
                'value' => function ($data) {
                    return Html::a($data->user->firstName . ' ' . $data->user->lastName, '/admin/user');
                },
            ],
            'firstName',
            'lastName',
            'phone',
            // 'v',
            [
                'attribute' => 'createdAt',
                'format' => 'datetime',
            ],
            // 'updatedAt',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
