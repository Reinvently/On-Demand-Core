<?php
/**
 * @copyright Reinvently (c) 2019
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

use reinvently\ondemand\core\vendor\tasker\models\TaskerCyclicTask;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel \reinvently\ondemand\core\vendor\tasker\models\TaskerCyclicTaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Tasker Cyclic Tasks');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tasker-cyclic-task-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Tasker Cyclic Task'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'timeLastRun:datetime',
            'timeInterval:datetime',
            'timeNextRun:datetime',
//            'status',
            [
                'format' => 'raw',
                'attribute' => 'status',
                'label' => 'Status',
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'status',
                    TaskerCyclicTask::getStatusStrings(),
                    ['class' => 'form-control', 'style' => 'padding-left: 0px; padding-right: 0px;']
                ),
                'value' => function ($searchModel) {
                    /** @var TaskerCyclicTask $searchModel */
                    return TaskerCyclicTask::getStatusString($searchModel->status);
                },
            ],
            'timeLastStatus:datetime',
            'cmd',
            'data:ntext',
            'log:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
