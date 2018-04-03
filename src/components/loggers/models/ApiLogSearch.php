<?php
/**
 * @copyright Reinvently (c) 2018
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\components\loggers\models;

use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class ApiLogSearch extends ApiLog
{
    /** @var  integer */
    public $moreId;
    /** @var  integer */
    public $lessId;

    /** @var string */
    public $startedAtFilter;
    /** @var string */
    public $finishedAtFilter;

    /** @var string with delimiter ',' */
    public $exceptRoutes;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userId'], 'integer'],
            [['response', 'request', 'exceptRoutes'], 'string', 'max' => 255],
            [['token', 'route'], 'string', 'max' => 255],
            [['moreId', 'lessId', 'ip', 'startedAtFilter', 'finishedAtFilter'], 'safe'],
        ];
    }

    public function getRoutes()
    {
        $response = static::find()->select(['route'])->groupBy(['route'])->orderBy(['route' => SORT_ASC])->all();
        if (!$response) {
            return [];
        }
        return ArrayHelper::map($response, 'route', 'route');
    }

    public function search($params)
    {
        $query = ApiLog::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => ['id', 'userId', 'route', 'startedAt', 'finishedAt'],
                'defaultOrder' => ['id' => SORT_DESC]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if (!empty($this->ip)) {
            $query->andFilterWhere(['=', 'ip', ip2long($this->ip)]);
        }

        $query->andFilterWhere(['>=', 'id', $this->moreId]);
        $query->andFilterWhere(['<=', 'id', $this->lessId]);
        $query->andFilterWhere(['userId' => $this->userId]);
        $query->andFilterWhere(['like', 'route', $this->route]);
        if ($this->exceptRoutes) {
            $query->andFilterWhere(['not like', 'route',
                explode(',', trim(str_replace(' ', '', $this->exceptRoutes), ','))]);
        }
        if ($this->startedAtFilter) {
            $query->andFilterWhere(['>', 'startedAt', strtotime($this->startedAtFilter)]);
        }
        if ($this->finishedAtFilter) {
            $query->andFilterWhere(['<', 'finishedAt', strtotime($this->finishedAtFilter)]);
        }
        $query->andFilterWhere(['like', 'request', $this->request]);
        $query->andFilterWhere(['like', 'response', $this->response]);
        return $dataProvider;
    }
}