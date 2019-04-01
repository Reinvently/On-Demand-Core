<?php
/**
 * @copyright Reinvently (c) 2019
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model \reinvently\ondemand\core\vendor\tasker\models\TaskerTask */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tasker Tasks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="tasker-task-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'timeNextRun:datetime',
//            'status',
            [
                'format' => 'raw',
                'attribute' => 'status',
                'label' => 'Status',
                'value' => \reinvently\ondemand\core\vendor\tasker\models\TaskerTask::getStatusString($model->status),
            ],
            'timeLastStatus:datetime',
            'cmd',
            'data:ntext',
            'log:ntext',
        ],
    ]) ?>

</div>
