<?php
/**
 * @copyright Reinvently (c) 2019
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

use reinvently\ondemand\core\vendor\tasker\models\TaskerTask;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel \reinvently\ondemand\core\vendor\tasker\models\TaskerTaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Tasker Tasks');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tasker-task-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Tasker Task'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'timeNextRun:datetime',
//            'status',
            [
                'format' => 'raw',
                'attribute' => 'status',
                'label' => 'Status',
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'status',
                    TaskerTask::getStatusStrings(),
                    ['class' => 'form-control', 'style' => 'padding-left: 0px; padding-right: 0px;']
                ),
                'value' => function ($searchModel) {
                    /** @var TaskerTask $searchModel */
                    return TaskerTask::getStatusString($searchModel->status);
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
