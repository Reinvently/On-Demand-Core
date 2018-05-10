<?php
/**
 * @copyright Reinvently (c) 2018
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\modules\socialauth\controllers;


use reinvently\ondemand\core\controllers\admin\AdminController;
use reinvently\ondemand\core\modules\socialauth\handlers\AuthHandler;
use yii\authclient\AuthAction;

class SocialAuthController extends AdminController
{
    public function actions()
    {
        return [
            'auth' => [
                'class' => AuthAction::class,
                'successCallback' => [$this, 'onAuthSuccess'],
            ],
        ];
    }

    public function onAuthSuccess($client)
    {
        (new AuthHandler($client))->handle();
    }
}