<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\modules\location\controllers;


use reinvently\ondemand\core\controllers\rest\ApiController;
use reinvently\ondemand\core\modules\location\models\Point;
use reinvently\ondemand\core\modules\location\models\Type;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;

abstract class ApiPointController extends ApiController
{
    /** @var Point */
    public $modelClass = Point::class;

    public function actionSearchByCircle($latitude, $longitude, $radius)
    {
        if (!is_numeric($latitude) || !is_numeric($longitude) || !is_numeric($radius)) {
            throw new BadRequestHttpException('Params mast be numeric: latitude, longitude, radius');
        }

        $modelClass = $this->modelClass;
        if ($modelClass::modelType() == Type::LOCATION) {
            $query = $modelClass::findByCircle($latitude, $longitude, $radius);
        } else {
            $query = $modelClass::findByTypeCircle($latitude, $longitude, $radius);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        return $dataProvider;
    }

    public function behaviors()
    {
        $verbs = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'search-by-circle' => ['get'],
                ]
            ],
        ];
        return ArrayHelper::merge($verbs, parent::behaviors());
    }

}