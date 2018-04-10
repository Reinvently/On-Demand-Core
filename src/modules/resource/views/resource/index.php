<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel \reinvently\ondemand\core\modules\resource\models\ResourceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Resources';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="resource-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Resource', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            [
                'label' => 'Image',
                'format' => 'raw',
                'value' => function($model) {
                    /** @var \reinvently\ondemand\core\modules\resource\models\Resource $model */
                    if ($model->getThumbnailImageUrl()) {
                        return Html::img($model->getThumbnailImageUrl());
                    }
                    return '';
                }
            ],
            [
                'label' => 'Image Size',
                'format' => 'raw',
                'value' => function($model) {
                    /** @var \reinvently\ondemand\core\modules\resource\models\Resource $model */
                    return $model->getImageSize();
                }
            ],
            [
                'attribute' => 'type',
                'format' => 'raw',
                'filter' => false,
                'value' => function($model) {
                    /** @var \reinvently\ondemand\core\modules\resource\models\Resource $model */
                    return $model->getTypeName();
                }
            ],
            'title',
            'description:ntext',
            'alias',
            [
                'label' => 'Related Record',
                'format' => 'raw',
                'value' => function($model) {
                    /** @var \reinvently\ondemand\core\modules\resource\models\Resource $model */
                    return $model->getRelatedRecordLinks();
                }
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
