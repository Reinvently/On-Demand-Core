<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use reinvently\ondemand\core\components\helpers\DateHelper;
use yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $model reinvently\ondemand\core\modules\promocode\models\PromoCode */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="promocode-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'promoType')->hiddenInput(['value' => $model::PROMO_TYPE_MANUAL])->label(false); ?>

    <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->dropDownList($model::$types) ?>

    <?= $form->field($model, 'amount')->textInput() ?>

    <?= $form->field($model, 'minAmount')->textInput() ?>

    <?= $form->field($model, 'days')->dropDownList(DateHelper::weekList(), ['multiple' => true, 'size' => 7]) ?>

    <?= $form->field($model, 'startAtDate')->textInput()->widget(DatePicker::className(), [
        'language' => 'en',
        'dateFormat' => 'yyyy-MM-dd',
    ]) ?>

    <?= $form->field($model, 'expireAtDate')->textInput()->widget(DatePicker::className(), [
        'language' => 'en',
        'dateFormat' => 'yyyy-MM-dd',
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
