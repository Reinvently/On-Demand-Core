<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel app\models\UserSearch */
/** @var string $roleId */

$this->title = 'Extra Fee';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Add extra fee', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'title',
            [
                'label' => 'Tariff',
                'attribute' => 'tariffId',
                'format' => 'raw',
                'value' => function ($data) {
                    return $data->tariff ? $data->tariff->name : '---';
                }
            ],
            'description:ntext',
            [
                'label' => 'Time (Dallas)',
                'format' => 'raw',
                'value' => function ($model) {
                    return (new DateTime($model->extraFeeTime->timeStart))
                        ->setTimezone(new DateTimeZone('America/Chicago'))
                        ->format("g:i:s A")
                    . " - " .
                    (new DateTime($model->extraFeeTime->timeFinish))
                        ->setTimezone(new DateTimeZone('America/Chicago'))
                        ->format("g:i:s A");

                },
                'contentOptions' => ['style' => 'width: 120px;'],
            ],
            [
                'label' => 'Time (UTC)',
                'format' => 'raw',
                'value' => function ($model) {
                    $timeStart = new DateTime($model->extraFeeTime->timeStart);
                    $timeFinish = new DateTime($model->extraFeeTime->timeFinish);
                    return $timeStart->format("g:i:s A") . " - " . $timeFinish->format("g:i:s A");
                },
                'contentOptions' => ['style' => 'width: 120px;'],
            ],
            [
                'label' => 'Days',
                'format' => 'raw',
                'value' => function ($model) {
                    return implode(", ", array_map(function ($el) {
                        return $el->day;
                    }, $model->extraFeeDay));
                }
            ],
            [
                'label' => 'Weekdays',
                'format' => 'raw',
                'value' => function ($model) {
                    return implode(", ", array_map(function ($el) {
                        return date("D", strtotime("Sunday + {$el->weekday} days"));
                    }, $model->extraFeeWeekday));
                }
            ],
            [
                'label' => 'Months',
                'format' => 'raw',
                'value' => function ($model) {
                    return implode(", ", array_map(function ($el) {
                        return DateTime::createFromFormat('!m', $el->month)->format("M");
                    }, $model->extraFeeMonth));
                },
                'contentOptions' => ['style' => 'width: 250px;'],
            ],
            [
                'label' => 'Years',
                'format' => 'raw',
                'value' => function ($model) {
                    return implode(", ", array_map(function ($el) {
                        return $el->year;
                    }, $model->extraFeeYear));
                },
                'contentOptions' => ['style' => 'width: 350px;'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
                'urlCreator' => function ($action, $model, $key, $index) {
                    return [$action, 'id' => $model->id];
                },
            ],
        ],
    ]); ?>

</div>
