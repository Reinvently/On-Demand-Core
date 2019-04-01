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
 * TaskerTaskSearch represents the model behind the search form of `app\models\TaskerTask`.
 */
class TaskerTaskSearch extends TaskerTask
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'timeNextRun', 'status', 'timeLastStatus'], 'integer'],
            [['cmd', 'data', 'log'], 'safe'],
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
        $query = TaskerTask::find();

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
            'timeNextRun' => $this->timeNextRun,
            'status' => $this->status,
            'timeLastStatus' => $this->timeLastStatus,
        ]);

        $query->andFilterWhere(['like', 'cmd', $this->cmd])
            ->andFilterWhere(['like', 'data', $this->data])
            ->andFilterWhere(['like', 'log', $this->log]);

        return $dataProvider;
    }
}
