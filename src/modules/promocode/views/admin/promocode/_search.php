<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model reinvently\ondemand\core\modules\promocode\models\SearchPromocode */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="promocode-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'code') ?>

    <?= $form->field($model, 'type') ?>

    <?= $form->field($model, 'promoType') ?>

    <?= $form->field($model, 'userId') ?>

    <?php // echo $form->field($model, 'amount') ?>

    <?php // echo $form->field($model, 'minAmount') ?>

    <?php // echo $form->field($model, 'usedCount') ?>

    <?php // echo $form->field($model, 'days') ?>

    <?php // echo $form->field($model, 'startAt') ?>

    <?php // echo $form->field($model, 'expireAt') ?>

    <?php // echo $form->field($model, 'createdAt') ?>

    <?php // echo $form->field($model, 'updatedAt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
