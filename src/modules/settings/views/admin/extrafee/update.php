<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model reinvently\ondemand\core\modules\settings\models\extrafee\ExtraFee */
/* @var $tariffs reinvently\ondemand\core\modules\settings\models\Tariff[] */

$this->title = 'Update Extra Fee: ' . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Extra Fee', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';

$dates = $model->getDates()->asArray();

?>
<div class="extra-fee-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'tariffs' => $tariffs,
        'selectedYears' => $dates["years"],
        'selectedMonths' => $dates["months"],
        'selectedWeekdays' => $dates["weekdays"],
        'selectedDays' => $dates["days"],
    ]) ?>

</div>
