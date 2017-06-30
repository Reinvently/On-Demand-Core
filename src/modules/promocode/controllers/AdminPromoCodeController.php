<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\promocode\controllers;

use reinvently\ondemand\core\controllers\admin\AdminController;
use reinvently\ondemand\core\modules\promocode\models\PromoCode;
use reinvently\ondemand\core\modules\promocode\models\SearchPromoCode;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AdminPromoCodeController implements the CRUD actions for PromoCode model.
 */
class AdminPromoCodeController extends AdminController
{
    public $modelClass = PromoCode::class;

    private $viewPath = '@app/core/modules/promocode/views/admin/promocode/';

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all PromoCode models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchPromoCode();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);

        return $this->render($this->viewPath . 'index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'modelClass' => $this->modelClass,
        ]);
    }

    /**
     * Displays a single PromoCode model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render($this->viewPath . 'view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new PromoCode model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $modelClass = $this->modelClass;
        /** @var PromoCode $model */
        $model = new $modelClass;

        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render($this->viewPath . 'create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing PromoCode model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render($this->viewPath . 'update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing PromoCode model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the PromoCode model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PromoCode the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        /** @var PromoCode $modelClass */
        $modelClass = $this->modelClass;
        if (($model = $modelClass::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
