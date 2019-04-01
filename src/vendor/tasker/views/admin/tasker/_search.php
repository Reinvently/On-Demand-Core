<?php
/**
 * @copyright Reinvently (c) 2019
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \reinvently\ondemand\core\vendor\tasker\models\TaskerSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tasker-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'status') ?>

    <?= $form->field($model, 'timeStart') ?>

    <?= $form->field($model, 'timeLastActivity') ?>

    <?= $form->field($model, 'processId') ?>

    <?php // echo $form->field($model, 'currentTaskId') ?>

    <?php // echo $form->field($model, 'currentCyclicTaskId') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
