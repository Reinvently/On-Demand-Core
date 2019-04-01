<?php
/**
 * @copyright Reinvently (c) 2019
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \reinvently\ondemand\core\vendor\tasker\models\Tasker */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tasker-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'status')->dropDownList($model::getStatusStrings()) ?>

    <?= $form->field($model, 'timeStart')->textInput() ?>

    <?= $form->field($model, 'timeLastActivity')->textInput() ?>

    <?= $form->field($model, 'processId')->textInput() ?>

    <?= $form->field($model, 'currentTaskId')->textInput() ?>

    <?= $form->field($model, 'currentCyclicTaskId')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
