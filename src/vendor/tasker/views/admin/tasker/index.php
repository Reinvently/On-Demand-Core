<?php
/**
 * @copyright Reinvently (c) 2019
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

use reinvently\ondemand\core\vendor\tasker\models\Tasker;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel \reinvently\ondemand\core\vendor\tasker\models\TaskerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Taskers');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tasker-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Tasker'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

            'id',
//            'status',
            [
                'format' => 'raw',
                'attribute' => 'status',
                'label' => 'Status',
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'status',
                    Tasker::getStatusStrings(),
                    ['class' => 'form-control', 'style' => 'padding-left: 0px; padding-right: 0px;']
                ),
                'value' => function ($searchModel) {
                    /** @var Tasker $searchModel */
                    return Tasker::getStatusString($searchModel->status);
                },
            ],

            'timeStart:datetime',
            'timeLastActivity:datetime',
            'processId',
//            'currentTaskId',
//            'currentCyclicTaskId',
            [
                'format' => 'raw',
                'attribute' => 'currentTaskId',
                'value' => function($model) {
                    /** @var Tasker $model */
                    return Html::a($model->currentTaskId, '/admin/tasker-task/index?TaskerTaskSearch[id]=' . $model->currentTaskId);
                },
            ],
            [
                'format' => 'raw',
                'attribute' => 'currentCyclicTaskId',
                'value' => function($model) {
                    /** @var Tasker $model */
                    return Html::a($model->currentCyclicTaskId, '/admin/tasker-cyclic-task/index?TaskerCyclicTaskSearch[id]=' . $model->currentCyclicTaskId);
                },
            ],


            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
