<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel reinvently\ondemand\core\modules\promocode\models\SearchPromoCode */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $modelClass \reinvently\ondemand\core\modules\promocode\models\PromoCode */

$this->title = 'Promo Codes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="promocode-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Promo Code', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'code',
            'amount',
            [
                'label' => 'Type',
                'attribute' => 'type',
                'filter' => $modelClass::$types,
                'format' => 'raw',
                'value' => function ($data) use ($modelClass) {
                    return $modelClass::$types[$data->type];
                },
            ],
            //'promoType',
            [
                'label' => 'User',
                'attribute' => 'userId',
                'format' => 'raw',
                'value' => function ($data) {
                    return ($data->user ? $data->user->getName() : '') . ' (ID: ' . $data->userId . ')';
                },
            ],
            'minAmount',
            [
                'label' => 'Days',
                'format' => 'raw',
                'value' => function ($data) {
                    return str_replace(' ', '<br />', $data->getDaysView());
                }
            ],
            [
                'attribute' => 'startAt',
                'format' => 'datetime',
            ],
            [
                'attribute' => 'expireAt',
                'format' => 'datetime',
            ],
            'usedCount',
            [
                'attribute' => 'createdAt',
                'format' => 'datetime',
            ],
            // 'updatedAt',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
