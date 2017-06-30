<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\settings\controllers;

use reinvently\ondemand\core\controllers\admin\AdminController;
use reinvently\ondemand\core\modules\settings\models\extrafee\ExtraFee;
use reinvently\ondemand\core\modules\settings\models\Tariff;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;

/**
 * Class AdminExtraFeeController
 * @package reinvently\ondemand\core\modules\settings\controllers
 */
class AdminExtraFeeController extends AdminController
{
    public $modelClass = ExtraFee::class;
    public $tariffClass = Tariff::class;

    /**
     * @return string
     */
    public function actionIndex()
    {
        /** @var ExtraFee $modelClass */
        $modelClass = $this->modelClass;
        $dataProvider = new ArrayDataProvider([
            'allModels' => $modelClass::getExtraFees()
        ]);

        return $this->render('@app/core/modules/settings/views/admin/extrafee/index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new $this->modelClass;

        $post = \Yii::$app->request->post();

        if ($model->load($post) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            var_dump($model->getErrors());
            return $this->render('@app/core/modules/settings/views/admin/extrafee/create', [
                'model' => $model,
                'tariffs' => $this->getTariffMap(),
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
            return $this->render('@app/core/modules/settings/views/admin/extrafee/update', [
                'model' => $model,
                'tariffs' => $this->getTariffMap(),
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
        $modelClass = $this->modelClass;
        if (($model = $modelClass::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @return array
     */
    private function getTariffMap()
    {
        $tariffClass = $this->tariffClass;
        /** @var Tariff $tariffs */
        $tariffs = $tariffClass::find()->all();
        return ArrayHelper::map($tariffs, 'id', 'name');
    }
}