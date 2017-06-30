<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model reinvently\ondemand\core\modules\settings\models\ExtraFee */
/* @var $tariffs reinvently\ondemand\core\modules\settings\models\Tariff[] */

$this->title = 'Add Extra Fee';
$this->params['breadcrumbs'][] = ['label' => 'Extra fee', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'tariffs' => $tariffs,
    ]) ?>

</div>
