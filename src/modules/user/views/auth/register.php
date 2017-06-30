<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */
/** @var User $user */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
?>
<h2>Registration</h2>
<?php $form = ActiveForm::begin([
    'id' => 'login-form',
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
        'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
        'labelOptions' => ['class' => 'col-lg-1 control-label'],
    ],
]); ?>

<?= $form->field($user, 'email') ?>
<?= $form->field($user, 'password')->passwordInput() ?>
<?= $form->field($user, 'firstName') ?>
<?= $form->field($user, 'lastName') ?>
<?= $form->field($user, 'phone') ?>

<div class="form-group">
    <div class="col-lg-offset-1 col-lg-11">
        <?= Html::submitButton('Register', ['class' => 'btn btn-primary', 'name' => 'register-button']) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>
