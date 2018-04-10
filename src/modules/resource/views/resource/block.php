<?php

/* @var $this yii\web\View */
/* @var $model \reinvently\ondemand\core\modules\resource\models\Resource */
?>
<?php if ($model->type == \reinvently\ondemand\core\modules\resource\models\Resource::TYPE_IMAGE): ?>
    <?= \yii\widgets\DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'id',
                'format' => 'raw',
                'value' => \yii\helpers\Html::a($model->id, \yii\helpers\Url::to(['admin/resource/view', 'id' => $model->id])),
            ],
            'title',
            'alias',
            [
                'label' => 'Image',
                'format' => 'raw',
                'value' => \yii\helpers\Html::a(
                    \yii\helpers\Html::img($model->getThumbnailImageUrl()),
                    $model->getUrl(),
                    ['target' => '_blank']
                ),
            ],
            [
                'label' => 'Image Size',
                'format' => 'raw',
                'value' => $model->getImageSize(),
            ],
//            [
//                'label' => 'Path',
//                'format' => 'raw',
//                'value' => $model->generateRootPath(),
//            ],
//            [
//                'label' => 'URL',
//                'format' => 'raw',
//                'value' => \yii\helpers\Html::a($model->getUrl(), $model->getUrl(), ['target' => '_blank']),
//            ],
        ],
    ]) ?>
<?php else: ?>
    <?= \yii\widgets\DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'id',
                'format' => 'raw',
                'value' => \yii\helpers\Html::a($model->id, \yii\helpers\Url::to(['admin/resource/view', 'id' => $model->id])),
            ],
            'title',
            'alias',
            [
                'label' => 'Path',
                'format' => 'raw',
                'value' => $model->generateRootPath(),
            ],
            [
                'label' => 'URL',
                'format' => 'raw',
                'value' => \yii\helpers\Html::a($model->getUrl(), $model->getUrl(), ['target' => '_blank']),
            ],
        ],
    ]) ?>

<?php endif; ?>