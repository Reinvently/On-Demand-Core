<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

use miloschuman\highcharts\Highcharts;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Order Stats';
$this->params['breadcrumbs'][] = $this->title;
?>
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="row">
        <div class="col-xs-3">
            <?php
            $form = ActiveForm::begin([
                'id' => 'filter-form',
                'options' => ['class' => 'form-vertical'],
            ]) ?>

            <?= $form->field($formModel, 'userId')->dropDownList($users) ?>
            <?= $form->field($formModel, 'dateStart')->widget(\yii\jui\DatePicker::class, [
                //'language' => 'ru',
                'dateFormat' => 'yyyy-MM-dd',
            ]); ?>
            <?= $form->field($formModel, 'dateFinish')->widget(\yii\jui\DatePicker::class, [
                //'language' => 'ru',
                'dateFormat' => 'yyyy-MM-dd',
            ]); ?>

            <div class="form-group">
                <div class="col-lg-offset-1 col-lg-11">
                    <?= Html::submitButton('Filter', ['class' => 'btn btn-primary']) ?>
                </div>
            </div>
            <?php ActiveForm::end() ?>
        </div>
    </div>
<?php
if ($xAxis) {
    echo Highcharts::widget([
        'options' => [
            'title' => ['text' => 'Order statistic'],
            'xAxis' => [
                'categories' => array_keys($xAxis),
            ],
            'yAxis' => [
                'title' => ['text' => 'Orders count']
            ],
            'series' => [
                //['name' => 'Orders', 'data' => array_values($xAxis)],
                //['name' => 'Orders', 'data' => [0,0,0,0,5]],
                ['name' => 'Orders', 'data' => array_values($xAxis)],
            ]
        ]
    ]);
}