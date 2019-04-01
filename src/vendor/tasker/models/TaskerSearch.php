<?php
/**
 * @copyright Reinvently (c) 2019
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\vendor\tasker\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * TaskerSearch represents the model behind the search form of `app\models\Tasker`.
 */
class TaskerSearch extends Tasker
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status', 'timeStart', 'timeLastActivity', 'processId', 'currentTaskId', 'currentCyclicTaskId'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Tasker::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'timeStart' => $this->timeStart,
            'timeLastActivity' => $this->timeLastActivity,
            'processId' => $this->processId,
            'currentTaskId' => $this->currentTaskId,
            'currentCyclicTaskId' => $this->currentCyclicTaskId,
        ]);

        return $dataProvider;
    }
}
