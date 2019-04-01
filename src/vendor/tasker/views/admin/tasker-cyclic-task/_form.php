<?php
/**
 * @copyright Reinvently (c) 2019
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \reinvently\ondemand\core\vendor\tasker\models\TaskerCyclicTask */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tasker-cyclic-task-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'timeLastRun')->textInput() ?>

    <?= $form->field($model, 'timeInterval')->textInput() ?>

    <?= $form->field($model, 'timeNextRun')->textInput() ?>

    <?= $form->field($model, 'status')->dropDownList($model::getStatusStrings()) ?>

    <?= $form->field($model, 'timeLastStatus')->textInput() ?>

    <?= $form->field($model, 'cmd')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'data')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'log')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
