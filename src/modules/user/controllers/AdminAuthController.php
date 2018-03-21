<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\user\controllers;

use reinvently\ondemand\core\controllers\admin\AdminController;
use reinvently\ondemand\core\modules\user\models\AuthModel;
use yii\filters\AccessControl;

/**
 * Class AdminAuthController
 * @package reinvently\ondemand\core\modules\user\controllers
 */
class AdminAuthController extends AdminController
{
    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'login', 'logout'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'login'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['logout'],
                        'roles' => ['@'],
                    ]
                ],
            ],
        ];
    }

    /**
     *
     */
    public function actionIndex()
    {
        $this->redirect(\Yii::$app->urlManager->createUrl('/admin/auth/login'));
    }

    /**
     * @return string
     */
    public function actionLogin()
    {
        /** @var AuthModel $model */
        $model = new AuthModel();
        if ($model->load(\Yii::$app->request->post()) and $model->login()) {
            $this->redirect(\Yii::$app->urlManager->createUrl('/admin'));
        } else {
            $this->layout = '@app/core/layouts/adminLogin';
            return $this->render('@app/core/modules/user/views/admin/auth/login', [
                'model' => $model,
            ]);
        }
    }

    /**
     *
     */
    public function actionLogout()
    {
        \Yii::$app->user->logout();
        $this->redirect(\Yii::$app->urlManager->createUrl('/admin'));
    }
}