<?php
use dosamigos\datepicker\DatePicker;

/** @var \reinvently\ondemand\core\components\loggers\models\ApiLogSearch $searchModel */
/** @var \yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Api Log';
$this->params['breadcrumbs'][] = $this->title;

$routeDataList = '<datalist id="routeDataList">';
foreach ($searchModel->getRoutes() as $route) {
    $routeDataList .= '<option value="' . $route . '"></option>';
}
$routeDataList .= '</datalist>';
?>
<div class="api-log-index">

	<h1><?= \yii\helpers\Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>


<?= \yii\grid\GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'tableOptions' => [
			'class' => 'table table-bordered',
		],
		'columns' => [
			[
				'attribute' => 'id',
				'value' => 'id',
				'filter' => '><input type="text" name="ApiLogSearch[moreId]" value="' . $searchModel->moreId . '" class="form-control"><br><<input type="text" name="ApiLogSearch[lessId]" value="' . $searchModel->lessId . '" class="form-control">'
			],
			'userId',
            [
                'attribute' => 'route',
                'filter' =>
					'Route:'
					. \yii\helpers\Html::activeInput(
						'text',
						$searchModel,
						'route',
						['list' => 'routeDataList', 'class' => 'form-control', 'style' => 'min-width:150px']
					)
					. $routeDataList
					. 'Except Routes with delimiter ",":'
					. \yii\helpers\Html::activeInput(
						'text',
						$searchModel,
						'exceptRoutes',
						['class' => 'form-control']
					),
			],
			[
				'attribute' => 'startedAt',
				'value' => 'startedAt',
                'format' => 'datetime',
				'filter' => DatePicker::widget([
					'model' => $searchModel,
					'attribute' => 'startedAtFilter',
					'clientOptions' => [
						'autoclose' => true,
						'format' => 'yyyy-mm-dd',
					]
				]),
            ],
			[
				'attribute' => 'finishedAt',
				'value' => 'finishedAt',
				'format' => 'datetime',
				'filter' => DatePicker::widget([
					'model' => $searchModel,
					'attribute' => 'finishedAtFilter',
					'clientOptions' => [
						'autoclose' => true,
						'format' => 'yyyy-mm-dd'
					]
				]),
			],
			[
				'attribute' => 'ip',
				'value' => function($searchModel) {
					return empty($searchModel->ip) ? '' : long2ip($searchModel->ip);
				},
			],
			[
				'attribute' => 'request',
				'value' => 'request',
				'format' => 'raw',
				'contentOptions'=>['style' => 'max-width: 400px; word-wrap:break-word;'],
			],
			[
				'attribute' => 'response',
				'value' => 'response',
				'format' => 'raw',
				'contentOptions'=>['style' => 'max-width: 400px; word-wrap:break-word;'],
			],
		]
	]); ?>


