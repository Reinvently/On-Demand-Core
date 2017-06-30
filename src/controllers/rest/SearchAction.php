<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\controllers\rest;


use reinvently\ondemand\core\components\model\CoreModel;
use reinvently\ondemand\core\components\model\CoreMongoDbModel;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\rest\Action;
use yii\web\BadRequestHttpException;

class SearchAction extends Action
{

    public $scenario = Model::SCENARIO_DEFAULT;

    public $viewAction = 'search';

    /**
     * @var callable
     */
    public $prepareDataProvider;
    public $params;

    /**
     * @return ActiveDataProvider
     */
    public function run()
    {
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id);
        }

        return $this->prepareDataProvider();
    }

    /**
     * Prepares the data provider that should return the requested collection of the models.
     * @return ActiveDataProvider
     */
    protected function prepareDataProvider()
    {
        if ($this->prepareDataProvider !== null) {
            return call_user_func($this->prepareDataProvider, $this);
        }

        /* @var $model CoreModel|CoreMongoDbModel */
        $model = new $this->modelClass([
            'scenario' => $this->scenario,
        ]);

        $model->load($this->params, '');

        $query = $model->searchFind();

        $params = array();
        foreach ($model->getAttributes() as $key => $value) {
            $params[$key] = $value;
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (empty($params)) {
            return $dataProvider;
        }

        foreach ($params as $param => $value) {
            $query->andFilterWhere([
                $param => $value,
            ]);
        }

        return $dataProvider;
    }

} 