<?php
/**
 * Created by PhpStorm.
 * User: sglushko
 * Date: 30.08.2017
 * Time: 17:45
 */

namespace reinvently\ondemand\core\modules\promocode\controllers;


use reinvently\ondemand\core\controllers\rest\ApiController;
use reinvently\ondemand\core\modules\promocode\models\PromoCode;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

abstract class ApiPromoCodeController extends ApiController
{
    /** @var PromoCode */
    public $modelClass = PromoCode::class;

    public function actionGetPriceAfterPromo($id, $price)
    {
        $modelClass = $this->modelClass;
        $model = $modelClass::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException();
        }

        $price = $model->getPriceAfterPromo($price, $this->getUser()->id);

        return $this->getTransport()->responseScalar($price);
    }

    public function behaviors()
    {
        $verbs = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'get-price-after-promo' => ['get'],
                ]
            ],
        ];
        return ArrayHelper::merge($verbs, parent::behaviors());
    }

}