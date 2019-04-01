<?php

use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \reinvently\ondemand\core\modules\resource\models\Resource */
/* @var string $jsCallback */
/* @var string $alias */
/* @var int $type */

if (empty($model)) {
    $model = new \reinvently\ondemand\core\modules\resource\models\Resource();
    if (!empty($alias)) {
        $model->alias = $alias;
    }
}


$callback = '';

if (
    $model->id
    && !$model->isNewRecord
    && !$model->hasErrors()
    && !empty($jsCallback)
) {
    $callback = '
var callback = ' . $jsCallback . ';
callback(' . $model->id . ');';
}

$blockId = 'block_' . mt_rand(0, PHP_INT_MAX);
if (empty($callbackId)) {
    $callbackId = 'callback_' . mt_rand(0, PHP_INT_MAX);
}
?>

<div id="<?= $blockId ?>">
    <?php
    $flagEndForm = false;
    if (empty($form)) {
        $form = ActiveForm::begin();
        $flagEndForm = true;
    }
    ?>

    <?= empty($jsCallback) ? '' : \yii\helpers\Html::hiddenInput('jsCallback', $jsCallback) ?>
    <?= \yii\helpers\Html::hiddenInput('callbackId', $callbackId) ?>
    <?= \yii\helpers\Html::hiddenInput('formOptionsJson', \yii\helpers\Json::encode(\yii\helpers\ArrayHelper::toArray(Yii::getObjectVars($form)))) ?>
    <?php if (isset($type)): ?>
        <?= \yii\helpers\Html::hiddenInput('Resource[type]', $type) ?>
    <?php else: ?>
        <?= $form->field($model, 'type', ['enableAjaxValidation' => false, 'enableClientValidation' => false])
            ->dropDownList($model->getTypeNames()) ?>
    <?php endif; ?>
    <?= $form->field($model, 'title', ['enableAjaxValidation' => false, 'enableClientValidation' => false])->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'description', ['enableAjaxValidation' => false, 'enableClientValidation' => false])->textarea(['rows' => 3]) ?>
    <?= $form->field($model, 'alias', ['enableAjaxValidation' => false, 'enableClientValidation' => false])->textInput(['maxlength' => true]) ?>

    <div class="panel panel-default">
        <div class="panel-body">
        <?php if ($model->getThumbnailImageUrl()): ?>
            <?= \yii\helpers\Html::img($model->getThumbnailImageUrl()) ?>
        <?php else: ?>
            <?= \yii\helpers\Html::a($model->getUrl(), $model->getUrl(), ['target' => '_blank']) ?>
        <?php endif; ?>
        </div>
    </div>


    <?= \dosamigos\fileupload\FileUpload::widget([
        'options' => ['id' => 'resource' . $blockId],
        'model' => $model,
        'attribute' => 'file',
        'url' => [\yii\helpers\Url::to('admin/resource/create-by-ajax')],
        'clientEvents' => [
            'fileuploadsubmit' => 'function(e, data) {
                            data.formData = function(form){
                                return $("#' . $blockId . ' [name]").serializeArray();
                            }
                        }',
            'fileuploaddone' => 'function(e, data) {
                            $(\'#' . $blockId . '\').replaceWith(data.result);
                            ' . $callbackId . '();
                        }',
            'fileuploadfail' => 'function(e, data) {;
                            alert("File uploading failed");
                            console.log(\'fail;\');
                            console.log(e);
                            console.log(data);
                        }',
        ],
    ]); ?>

    <?php
    if ($flagEndForm) {
        ActiveForm::end();
    }
    ?>

    <script type="text/javascript">
        $("#<?= $blockId ?> .fileinput-button span").text('Select file and save resource');
        var <?= $callbackId ?> = function () {
            <?= $callback ?>
        };
    </script>
</div>
