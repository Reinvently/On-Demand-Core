<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\settings\controllers;

use reinvently\ondemand\core\controllers\admin\AdminController;
use reinvently\ondemand\core\modules\settings\models\TariffForm;
use yii\data\ActiveDataProvider;

/**
 * Class AdminTariffController
 * @package reinvently\ondemand\core\modules\settings\controllers
 */
class AdminTariffController extends AdminController
{
    public $modelClass = TariffForm::class;

    /**
     * @return string
     */
    public function actionIndex()
    {
        /** @var TariffForm $modelClass */
        $modelClass = $this->modelClass;
        $dataProvider = new ActiveDataProvider([
            'query' => $modelClass::find(),
        ]);

        return $this->render('@app/core/modules/settings/views/admin/tariff/index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        /** @var TariffForm $model */
        $model = new $this->modelClass;

        $post = \Yii::$app->request->post();
        if ($model->load($post) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('@app/core/modules/settings/views/admin/tariff/create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * @param $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('@app/core/modules/settings/views/admin/tariff/update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        /** @var TariffForm $modelClass */
        $modelClass = $this->modelClass;
        if (($model = $modelClass::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}