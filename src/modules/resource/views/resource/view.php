<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model \reinvently\ondemand\core\modules\resource\models\Resource */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Resources', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="resource-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'type',
                'format' => 'raw',
                'filter' => false,
                'value' => $model->getTypeName(),
            ],
            'title',
            'description:ntext',
            'alias',
            'version',
            [
                'label' => 'Image',
                'format' => 'raw',
                'value' => $model->getThumbnailImageUrl() ? Html::img($model->getThumbnailImageUrl()) : '',
            ],
            [
                'label' => 'Image Size',
                'format' => 'raw',
                'value' => $model->getImageSize(),
            ],
            [
                'label' => 'Image Type',
                'format' => 'raw',
                'value' => $model->getImageType(),
            ],
            'extension',
            [
                'label' => 'Path',
                'format' => 'raw',
                'value' => $model->generateRootPath(),
            ],
            [
                'label' => 'URL',
                'format' => 'raw',
                'value' => HTML::a($model->getUrl(), $model->getUrl(), ['target' => '_blank']),
            ],
            'createAt:dateTime',
            'updateAt:dateTime',
            [
                'label' => 'Related Record',
                'format' => 'raw',
                'value' => $model->getRelatedRecordLinks(),
            ],

        ],
    ]) ?>

</div>
