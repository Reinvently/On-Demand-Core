<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\user\controllers;

use Yii;
use yii\web\Controller;
use reinvently\ondemand\core\modules\user\models\AuthModel;
use reinvently\ondemand\core\modules\user\models\User;

class AuthController extends Controller
{
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            $this->goHome();
        }

        /** @var AuthModel $form */
        $model = new AuthModel();

        if ($model->load(Yii::$app->request->post()) and $model->login()) {
            $this->goHome();
        }

        $data = [
            'model' => $model,
        ];
        return $this->render('@app/core/modules/user/views/auth/login', $data);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        $this->goHome();
    }

    public function actionRegister()
    {
        /** @var User $user */
        $user = new User;

        if (Yii::$app->request->post()) {
            $user->setAttributes(Yii::$app->request->post('User'));
            if($user->save()) {
                $this->redirect(Yii::$app->getUrlManager()->createUrl('/auth/login'));
            }
        }

        $data = [
            'user' => $user
        ];
        return $this->render('@app/core/modules/user/views/auth/register', $data);
    }


}