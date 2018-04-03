<?php

use reinvently\ondemand\core\components\loggers\models\ExceptionLog;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Exception Log';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="api-exception-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'rowOptions' => function($model, $key, $index, $grid) {
            /** @var ExceptionLog $model */
            if($model->isFailed) {
                return ['style' => 'background : #FE6F5E;'];
            }
            return [];
        },
        'tableOptions' => [
            'class' => 'table table-bordered',
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'label' => 'Creation Time UTC',
                'value' => function($model) {
                    $date = DateTime::createFromFormat('U', $model->datetime);
                    return $date->format("M j, Y g:i:s A");
                },
                'contentOptions' => ['style' => 'min-width:110px;'],
            ],
            'route',
            'userId',
            'message:ntext',
            'fileName',
            'lineFile',
            'stackTrace:ntext',
            [
                'attribute' => 'ip',
                'value' => function($searchModel) {
                    return empty($searchModel->ip) ? '' : long2ip($searchModel->ip);
                },
            ],
            [
                'attribute' => 'request',
                'format' => 'raw',
                'contentOptions'=>['style' => 'max-width: 400px; word-wrap:break-word;'],
                'filter' => false
            ],
//            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
