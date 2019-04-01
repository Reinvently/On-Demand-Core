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
use yii\data\Pagination;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\rest\Action;
use yii\web\ForbiddenHttpException;

class SearchAction extends Action
{

    public $scenario = Model::SCENARIO_DEFAULT;

    public $viewAction = 'search';

    /** @var array|Pagination|bool $value */
    public $pagination;

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
        return $this->prepareDataProvider();
    }

    /**
     * Prepares the data provider that should return the requested collection of the models.
     * @return ActiveDataProvider
     */
    protected function prepareDataProvider()
    {
        $this->preparePagination();

        $params = ArrayHelper::merge(\Yii::$app->request->getQueryParams(), \Yii::$app->request->getBodyParams());
        if ($this->prepareDataProvider !== null) {
            return call_user_func($this->prepareDataProvider, $this);
        }

        /* @var $model CoreModel|CoreMongoDbModel */
        $model = new $this->modelClass([
            'scenario' => $this->scenario,
        ]);

        $model->load($params, '');

        $query = $model->searchFind();

        $this->prepareSubModel($model, $query, $params, '');

        $this->prepareOrderByOld($query, $model, $params);
        $this->prepareOrderBy($query, $model, $params);

        $this->prepareCondition($query, $model, $params);

//        $model->load($params, '');
//        if ($model->hasAttribute('id') && $id = ArrayHelper::getValue($params, 'id')) {
//            $model->id = $id;
//        }

        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id, $model);
        }

//        $query = $model->searchFind();

//        $this->prepareWith($query, $model, $params);
//        $this->prepareOrderByOld($query, $model, $params);
//
//        $params = array();
//        foreach ($model->getAttributes() as $key => $value) {
//            $params[$key] = $value;
//        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if ($this->pagination !== null) {
            $dataProvider->setPagination($this->pagination);
        }

//        if (empty($params)) {
//            return $dataProvider;
//        }
//
//        foreach ($params as $param => $value) {
//            $tableName = $model->tableName();
//            $query->andFilterWhere([
//                $tableName . '.' . $param => $value,
//            ]);
//        }

        return $dataProvider;
    }

    protected function preparePagination()
    {
        $request = \Yii::$app->request;
        if ($request->getMethod() == 'POST') {
            $params = $request->getQueryParams();
            $page = $request->getBodyParam('page');
            if ($page) {
                $params['page'] = $page;
            }

            $perPage = $request->getBodyParam('per-page');
            if ($perPage) {
                $params['per-page'] = $perPage;
            }
            $request->setQueryParams($params);
        }
    }

    /**
     * @param CoreModel|CoreMongoDbModel $model
     * @param ActiveQuery $query
     * @param array $params
     * @param string $path
     */
    protected function prepareSubModel($model, $query, $params, $path) {
//        /* @var $model CoreModel|CoreMongoDbModel */
//        $model = new $modelClass([
//            'scenario' => $this->scenario,
//        ]);

//        if ($model->hasAttribute('id') && $id = ArrayHelper::getValue($params, 'id')) {
//            $model->id = $id;
//        }

        $tableName = $model->tableName();

        $this->prepareWith($query, $model, $params, $path);

        $params = array();
        foreach ($model->getAttributes() as $key => $value) {
            $params[$key] = $value;
        }

        if (empty($params)) {
            return;
        }

        foreach ($params as $param => $value) {
            $query->andFilterWhere([
                $tableName . '.' . $param => $value,
            ]);
        }

    }

    /**
     * @param ActiveQuery $query
     * @param ActiveRecord $model
     * @param array $params
     * @param string $path
     */
    protected function prepareWith($query, $model, $params, $path)
    {
        if (!ArrayHelper::keyExists('with', $params)) {
            return;
        }

        $with = $params['with'];
        if (!is_array($with)) {
            return;
        }

        foreach ($with as $name) {
            /** @var $activeQuery ActiveQuery */
            $getter = 'get' . $name;
            if (method_exists($model, $getter)) {
                $activeQuery = $model->$getter();
                if ($activeQuery->modelClass) {
                    $path = ($path ? $path . '.' : '') . $name;
                    $query->joinWith($path);

                    /* @var $model CoreModel|CoreMongoDbModel */
                    $model = new $activeQuery->modelClass([
                        'scenario' => $this->scenario,
                    ]);
                    $subParams = ArrayHelper::getValue($params, $name);
                    $model->load($subParams, '');

                    if (ArrayHelper::getValue($params, $name)) {
                        $this->prepareSubModel(
                            $model,
                            $query,
                            $subParams,
                            $path
                        );
                    }
                }
            }
        }

    }

    /**
     * @todo Deprecated
     * @deprecated
     * @param ActiveQuery $query
     * @param ActiveRecord $model
     * @param array $params
     */
    protected function prepareOrderByOld($query, $model, $params)
    {
        if (!ArrayHelper::keyExists('order', $params)) {
            return;
        }

        $order = $params['order'];
        if (!is_array($order)) {
            return;
        }

        //todo needs validate unsafe attributes

        foreach ($order as $key => $value) {
            if ($value == '1') {
                $query->addOrderBy([
                    $key => SORT_ASC,
                ]);
            } elseif ($value == '-1') {
                $query->addOrderBy([
                    $key => SORT_DESC,
                ]);
            }
        }

    }

    /**
     * @param ActiveQuery $query
     * @param ActiveRecord $model
     * @param array $params
     */
    protected function prepareOrderBy($query, $model, $params)
    {
        if (!ArrayHelper::keyExists('orderBy', $params)) {
            return;
        }

        $order = $params['orderBy'];
        if (!is_array($order)) {
            return;
        }

        //todo needs validate unsafe attributes

        foreach ($order as $item) {
            if (!is_array($item) || count($item) != 1) {
                continue;
            }
            foreach ($item as $key => $value) {
                if ($value == '1') {
                    $query->addOrderBy([
                        $key => SORT_ASC,
                    ]);
                } elseif ($value == '-1') {
                    $query->addOrderBy([
                        $key => SORT_DESC,
                    ]);
                }
            }
        }

    }

    /**
     * @param ActiveQuery $query
     * @param CoreModel $model
     * @param array $params
     */
    protected function prepareCondition($query, $model, $params)
    {
        if (!ArrayHelper::keyExists('condition', $params)) {
            return;
        }

        $condition = $params['condition'];
        if (!is_array($condition)) {
            return;
        }

        $condition = $this->validateCondition($condition, $model);

        $query->andWhere($condition);
    }

    /**
     * @param array $condition
     * @param CoreModel $model
     * @return array
     */
    protected function validateCondition($condition, $model)
    {
        if (!is_array($condition)) {
            return $condition;
        }

//        $operators = [
//            '<>', '!=', '>=', '>', '<=', '<', '=',
//            'IS', 'IS NOT',
//            'NOT', 'AND', 'OR', 'BETWEEN', 'NOT BETWEEN',
//            'IN', 'NOT IN',
//            'LIKE', 'NOT LIKE', 'OR LIKE', 'OR NOT LIKE',
//            'EXISTS', 'NOT EXISTS',
//        ];

        $safeAttributes = $model->safeAttributes();
        $unsafeAttributes = $model->unsafeAttributes();
        $unsafeAttributes[] = '.'; //todo sub models coming soon
//        unset($unsafeAttributes[0]);// todo test delete 'id'
//        var_dump($unsafeAttributes);exit;

        if (!isset($condition[0])) {
            // hash format: 'column1' => 'value1', 'column2' => 'value2', ...
            foreach ($condition as $name => $value) {
                foreach($unsafeAttributes as $attribute) {
                    if (!is_numeric($name) && !in_array($name, $safeAttributes) && stripos($name, $attribute) !== false) {
                        throw new ForbiddenHttpException('Using unsafe attribute: ' . $attribute);
                    }
                }
            }

            return $condition;
        }

        // operator format: operator, operand 1, operand 2, ...

        $operator = array_shift($condition);

        foreach ($condition as $i => $operand) {
            $subCondition = $this->validateCondition($operand, $model);
            if (is_string($subCondition)) {
                $name = $subCondition;
                foreach($unsafeAttributes as $attribute) {
                    if (!is_numeric($name) && !in_array($name, $safeAttributes) && stripos($name, $attribute) !== false) {
                        throw new ForbiddenHttpException('Using unsafe attribute: ' . $attribute);
                    }
                }
            }
        }

        array_unshift($condition, $operator);

        return $condition;
    }

}