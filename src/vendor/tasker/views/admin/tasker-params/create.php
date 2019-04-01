<?php
/**
 * @copyright Reinvently (c) 2019
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \reinvently\ondemand\core\vendor\tasker\models\TaskerParams */

$this->title = Yii::t('app', 'Create Tasker Params');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tasker Params'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tasker-params-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
