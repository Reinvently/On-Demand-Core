<?php
/**
 * @copyright Reinvently (c) 2018
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\resource\controllers\admin;

use reinvently\ondemand\core\controllers\admin\AdminController;
use reinvently\ondemand\core\modules\resource\models\Resource as AbstractResource;
use reinvently\ondemand\core\modules\resource\models\ResourceSearch;
use Yii;
use yii\bootstrap\ActiveForm;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;

/**
 * ResourceController implements the CRUD actions for Resource model.
 */
class ResourceController extends AdminController
{
    /** @var string  */
    public $modelClass = AbstractResource::class;

    /** @var string  */
    public $modelSearchClass = ResourceSearch::class;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ]);
    }

    /**
     * Lists all Resource models.
     * @return mixed
     */
    public function actionIndex()
    {
        /** @var ResourceSearch $searchModel */
        $searchModel = new $this->modelSearchClass();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Resource model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Finds the Resource model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AbstractResource
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        /** @var AbstractResource $modelClass */
        $modelClass = $this->modelClass;

        /** @var AbstractResource $model */
        $model = $modelClass::find()
            ->with($modelClass::withRelatives())
            ->where(['id' => $id])
            ->one();

        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Creates a new Resource model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        /** @var AbstractResource $model */
        $model = new $this->modelClass();

        $model->setScenario(AdminController::CREATE_SCENARIO);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Resource model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $model->setScenario(AdminController::UPDATE_SCENARIO);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Resource model.
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
     * @return string
     */
    public function actionCreateByAjax()
    {
        /** @var AbstractResource $model */
        $model = new $this->modelClass();

        $model->load(Yii::$app->request->post());
        $model->save();

        return $this->renderAjax('formBlock', [
            'model' => $model,
            'form' => new ActiveForm(Json::decode(Yii::$app->request->post('formOptionsJson'))),
            'jsCallback' => Yii::$app->request->post('jsCallback'),
            'callbackId' => Yii::$app->request->post('callbackId'),
            'type' => $model->type,
        ]);
    }
}
