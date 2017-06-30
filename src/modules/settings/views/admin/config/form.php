<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $fields array */

?>

<?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
    <?php if ($fields): ?>
        <?php foreach ($fields as $f): ?>
            <div class="form-group">
                <?= Html::label($f['label']) ?>
                <?= Html::input('text', $f['attribute'], $f['value'], ['class' => 'form-control']) ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton('Update', ['class' => 'btn btn-primary']) ?>
    </div>
<?php ActiveForm::end(); ?>
