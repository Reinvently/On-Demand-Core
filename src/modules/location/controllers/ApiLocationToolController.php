<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */
/**
 * Created by PhpStorm.
 * User: sglushko
 * Date: 06.02.2016
 * Time: 21:22
 */

namespace reinvently\ondemand\core\modules\location\controllers;


use reinvently\ondemand\core\controllers\rest\ApiTameController;
use reinvently\ondemand\core\vendor\mapsdirections\addresses\AddressModel;
use reinvently\ondemand\core\vendor\mapsdirections\addresses\GeoAddress;
use reinvently\ondemand\core\vendor\mapsdirections\google\Directions;
use reinvently\ondemand\core\vendor\mapsdirections\google\RequestParams;
use reinvently\ondemand\core\vendor\mapsdirections\google\Response;
use reinvently\ondemand\core\vendor\mapsdirections\google\Types;

class ApiLocationToolController extends ApiTameController
{
    public function actionDistanceByPoints($fromLatitude, $fromLongitude, $toLatitude, $toLongitude)
    {
        if (
        !(
            is_numeric($fromLatitude) && is_numeric($fromLongitude)
            && is_numeric($toLatitude) && is_numeric($toLongitude)
        )
        ) {
            throw new BadRequestHttpException('Params mast be numeric: fromLatitude, fromLongitude, '
                . 'toLatitude, toLongitude');
        }

        $distances = null;
        $directions = \Yii::$app->mapsDirections;

        $address1 = new AddressModel();
        $address1->latitude = $fromLatitude;
        $address1->longitude = $fromLongitude;

        $address2 = new AddressModel();
        $address2->latitude = $toLatitude;
        $address2->longitude = $toLongitude;

        /** @var Response $response */
        $response = $directions->request(
            new GeoAddress($address1),
            new GeoAddress($address2)
        );

        if ($response->hasResults()) {
            $route = $response->getRoutes()[0];
            $distances = $route->getTotalDistance();
        }

        $distances /= 1000; //convert meters to kilometers

        return $this->getTransport()->responseScalar($distances);
    }

    public function behaviors()
    {
        $verbs = [
            'verbs' => [
                'class' => \yii\filters\VerbFilter::className(),
                'actions' => [
                    'distance-by-points' => ['get'],
                ]
            ],
        ];
        return array_merge_recursive($verbs, parent::behaviors());
    }

}