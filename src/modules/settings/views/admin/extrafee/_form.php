<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\time\TimePicker;

\reinvently\ondemand\core\modules\settings\assets\ExtraFeeAsset::register($this);

/* @var $this yii\web\View */
/* @var $model reinvently\ondemand\core\modules\settings\models\ExtraFee */
/* @var $form yii\widgets\ActiveForm */
/* @var $tariffs reinvently\ondemand\core\modules\settings\models\Tariff[] */

$dayPeriod = new \DatePeriod(
    (new \DateTime())->setDate(null, null, 1),
    new \DateInterval("P1D"),
    (new \DateTime())->setDate(null, null, 31)->modify("+1 days")
);

$weekdayPeriod = new DatePeriod(
    new DateTime("Monday this week"),
    new DateInterval("P1D"),
    new DateTime("Monday next week")
);

$monthPeriod = new DatePeriod(
    new DateTime("Jan this year"),
    new DateInterval("P1M"),
    new DateTime("Dec next year")
);

$yearPeriod = new DatePeriod(
    new DateTime("this year"),
    new DateInterval("P1Y"),
    new DateTime("+15 years")
);

$dayList = [];
foreach($dayPeriod as $d){
    $dayList[(int)$d->format("j")] = $d->format("j");
}

$weekdayList = [];
foreach($weekdayPeriod as $w){
    $weekdayList[(int)$w->format("N")] = $w->format("l");
}

$monthList = [];
foreach($monthPeriod as $m){
    $monthList[$m->format("n")] = $m->format("F");
}

$yearList = [];
foreach($yearPeriod as $y) {
    $yearList[$y->format("Y")] = $y->format("Y");
}

$errors = $model->getErrors();
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tariffId')->dropDownList($tariffs) ?>

    <?= $form->field($model, 'description')->textarea() ?>

    <div class="form-group<?php if(isset($errors["timeStart"])) {?> has-error <?php } ?>" style="float: left; margin-right:40px">
        <label class="control-label" for="extrafee-description">Time From (UTC)</label>
        <?= TimePicker::widget([
            'name' => 'ExtraFee[timeStart]',
            'value' => $model->extraFeeTime ? (new DateTime($model->extraFeeTime->timeStart))->format("g:i:s A") : '00:00:00 AM',
            'pluginOptions' => [
                'showSeconds' => true,
                'showMeridian' => true
            ],
            'containerOptions' => [
                "style" => "width: 270px"
            ]
        ])
        ?>
        <div>
            <div style="margin-top: 10px; margin-left: 10px; color:<?php if(!isset($errors["timeStart"])) { ?>green;<?php } ?> height: 20px">
                <span id="ectopicTimeFrom">
                    <?php
                    $timeStart = $model->extraFeeTime ? $model->extraFeeTime->timeStart : '00:00:00';
                    echo (new DateTime($timeStart))->setTimezone(new DateTimeZone('America/Chicago'))->format("g:i:s A")
                    ?>
                </span>
                (Dallas)
            </div>
        </div>
        <?php if(isset($errors["timeStart"])) {?>
            <div class="help-block"><?=is_array($errors["timeStart"][0]) ? implode("<br>", $errors["timeStart"][0]) : $errors["timeStart"][0] ?></div>
        <?php } ?>
    </div>
    <div class="form-group<?php if(isset($errors["timeFinish"])) {?> has-error <?php } ?>" style="float: left">
        <label class="control-label" for="extrafee-description">Time To (UTC)</label>
        <?= TimePicker::widget([
            'name' => 'ExtraFee[timeFinish]',
            'value' => $model->extraFeeTime ? (new DateTime($model->extraFeeTime->timeFinish))->format("g:i:s A") : '11:59:59 PM',
            'pluginOptions' => [
                'showSeconds' => true,
                'showMeridian' => true
            ],

            'containerOptions' => [
                "style" => "width: 270px"
            ]
        ])
        ?>
        <div style="margin-top: 10px; margin-left: 10px; color:<?php if(!isset($errors["timeFinish"])) { ?>green;<?php } ?> height: 20px">
            <span id="ectopicTimeTo">
                <?php
                $timeFinish = $model->extraFeeTime ? $model->extraFeeTime->timeFinish : '23:59:59';
                echo (new DateTime($timeFinish))->setTimezone(new DateTimeZone('America/Chicago'))->format("g:i:s A");
                ?>
            </span>
            (Dallas)
        </div>
        <?php if(isset($errors["timeFinish"])) {?>
            <div class="help-block"><?=is_array($errors["timeFinish"][0]) ? implode("<br>", $errors["timeFinish"][0]) : $errors["timeFinish"][0] ?></div>
        <?php } ?>
    </div>
    <div style="clear: left"></div>
    <div class="form-group" style="float: left; margin-right:40px">
        <label class="control-label" for="extrafee-description">Day</label>
        <div>
            <?= Html::dropDownList(
                "ExtraFee[days]",
                isset($selectedDays) ? $selectedDays : null,
                $dayList,
                [
                    'multiple' => true,
                    'size' => 10,
                    "style" => "width: 50px",
                    "id" => "extraFeeDays"
                ])
            ?>
        </div>
    </div>
    <div class="form-group" style="float: left; margin-right:40px">
        <label class="control-label" for="extrafee-description">Weekday</label>
        <div>
            <?= Html::dropDownList(
                "ExtraFee[weekdays]",
                isset($selectedWeekdays) ? $selectedWeekdays : array_keys($weekdayList),
                $weekdayList,
                [
                    'multiple' => true,
                    'size' => count($weekdayList),
                    "style" => "clear: both",
                    "id" => "extraFeeWeekdays",
                    "disabled" => (isset($selectedDays) and !empty($selectedDays)) ? true : false
                ])
            ?>
        </div>
    </div>
    <div class="form-group" style="float: left; margin-right:40px">
        <label class="control-label" for="extrafee-description">Month</label>
        <div>
            <?= Html::dropDownList(
                "ExtraFee[months]",
                isset($selectedMonths) ? $selectedMonths : array_keys($monthList),
                $monthList,
                [
                    'multiple' => true,
                    'size' => count($monthList),
                ])
            ?>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label" for="extrafee-description">Year</label>
        <div>
            <?= Html::dropDownList(
                "ExtraFee[years]",
                isset($selectedYears) ? $selectedYears : array_slice(array_keys($yearList), 0, 5),
                $yearList,
                [
                    'multiple' => true,
                    'size' => 7,
                ])
            ?>
        </div>
    </div>
    <div style="clear: left"></div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
